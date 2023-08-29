<?php
add_action( 'wp_ajax_wpestate_upload_image_property', 'wpestate_upload_image_property' );
add_action( 'wp_ajax_nopriv_wpestate_upload_image_property', 'wpestate_upload_image_property' );
if( !function_exists( 'wpestate_upload_image_property' ) ) :
    function wpestate_upload_image_property( ) {

       $submitted_file = $_FILES['floor_thumb_uploaded'];
       $uploaded_image = wp_handle_upload( $submitted_file, array( 'test_form' => false ) );


      echo json_encode( $uploaded_image );die();

       if ( isset( $uploaded_image['file'] ) ) {
           $file_name          =   basename( $submitted_file['name'] );
           $file_type          =   wp_check_filetype( $uploaded_image['file'] );

           // Prepare an array of post data for the attachment.
           $attachment_details = array(
               'guid'           => $uploaded_image['url'],
               'post_mime_type' => $file_type['type'],
               'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
               'post_content'   => '',
               'post_status'    => 'inherit'
           );

           $attach_id      =   wp_insert_attachment( $attachment_details, $uploaded_image['file'] );
           $attach_data    =   wp_generate_attachment_metadata( $attach_id, $uploaded_image['file'] );
           wp_update_attachment_metadata( $attach_id, $attach_data );

           $thumbnail_url = wp_get_attachment_image_src( $attach_id, 'small' );
           $feat_image_url = wp_get_attachment_url( $attach_id );

           $ajax_response = array(
               'success'          => true,
               'url'              => $thumbnail_url[0],
               'attachment_id'    => $attach_id,
               'full_image'       => $feat_image_url
           );

           echo json_encode( $ajax_response );
           die;

       } else {
           $ajax_response = array( 'success' => false, 'reason' => 'Image upload failed!' );
           echo json_encode( $ajax_response );
           die;
       }
    }
endif;





add_action('wp_ajax_wpestate_image_caption',  'wpestate_image_caption');
if( !function_exists('wpestate_image_caption') ):
    function wpestate_image_caption(){
        check_ajax_referer( 'wpestate_image_upload', 'security' );
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;

        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }


        $attach_id  =   intval($_POST['attach_id']);
        $caption    =   esc_html($_POST['caption']);
        $the_post   =   get_post( $attach_id);
        $agent_list                     =  (array) get_user_meta($userID,'current_agent_list',true);


        if (!current_user_can('manage_options') ){
            if( $userID != $the_post->post_author  &&  !in_array($the_post->post_author , $agent_list)) {
                exit('you don\'t have the right to edit this');;
            }
        }
        $my_post = array(
            'ID'           => $attach_id,
            'post_excerpt' => $caption,
        );

      // Update the post into the database
        wp_update_post( $my_post );

        exit;
    }
endif;


add_action('wp_ajax_nopriv_wpestate_me_upload',             'wpestate_me_upload');
add_action('wp_ajax_wpestate_me_upload',             'wpestate_me_upload');
add_action('wp_ajax_aaiu_delete',           'me_delete_file');
add_action('wp_ajax_wpestate_delete_file',  'wpestate_delete_file');


if( !function_exists('wpestate_delete_file') ):
    function wpestate_delete_file(){

        if(isset($_POST['isadmin']) && intval($_POST['isadmin'])==1 ){
            check_ajax_referer( 'wpestate_attach_delete', 'security' );
        }else{
            check_ajax_referer( 'wpestate_image_upload', 'security' );
        }
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;

        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }
        $attach_id  = intval($_POST['attach_id']);
        $the_post   = get_post( $attach_id);

        if (!current_user_can('manage_options') ){
            if( $userID != $the_post->post_author ) {
                exit('you don\'t have the right to delete this');;
            }
        }
        wp_delete_attachment($attach_id, true);
        exit;
    }
endif;


if( !function_exists('me_delete_file') ):
    function me_delete_file(){

        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;

        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }


        $attach_id  =   intval($_POST['attach_id']);
        $the_post   =   get_post( $attach_id);

        if( $current_user->ID != $the_post->post_author ) {
            exit('you don\'t have the right to delete this');;
        }

        wp_delete_attachment($attach_id, true);
        exit;
    }
endif;




if( !function_exists('wpestate_me_upload') ):
    function wpestate_me_upload(){
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;
        $filename       =   convertAccentsAndSpecialToNormal($_FILES['aaiu_upload_file']['tmp_name']);
        $base           =   '';
        $allowed_html   =   array();

        list($width, $height) = getimagesize($filename);

        if(isset($_GET['base'])){
            $base   =   esc_html( wp_kses( $_GET['base'], $allowed_html ) );
        }

        $file = array(
            'name'      => convertAccentsAndSpecialToNormal($_FILES['aaiu_upload_file']['name']),
            'type'      => $_FILES['aaiu_upload_file']['type'],
            'tmp_name'  => $_FILES['aaiu_upload_file']['tmp_name'],
            'error'     => $_FILES['aaiu_upload_file']['error'],
            'size'      => $_FILES['aaiu_upload_file']['size'],
            'width'     =>  $width,
            'height'    =>  $height,
            'base'      =>  $base
        );
        $file = fileupload_process($file);
    }
endif;





if( !function_exists('fileupload_process') ):
    function fileupload_process($file){


        if( $file['type']!='application/pdf'    ){
            if( intval($file['height'])<500 || intval($file['width']) <500 ){
                $response = array('success' => false,'image'=>true);
                print json_encode($response);
                exit;
            }
        }
        $attachment = handle_file($file);
        if (is_array($attachment)) {
            $html = getHTML($attachment);
            $response = array(
                'base' =>  $file['base'],
                'type'      =>  $file['type'],
                'height'      =>  $file['height'],
                'width'      =>  $file['width'],
                'success'   => true,
                'html'      => $html,
                'attach'    => $attachment['id'],


            );

            print json_encode($response);
            exit;
        }

        $response = array('success' => false);
        print json_encode($response);
        exit;
    
        
}
endif;




if( !function_exists('handle_file') ):
    function handle_file($upload_data){
        $return = false;


        $uploaded_file = wp_handle_upload($upload_data, array('test_form' => false));

        if (isset($uploaded_file['file'])) {
            $file_loc   =   $uploaded_file['file'];
            $file_name  =   basename($upload_data['name']);
            $file_type  =   wp_check_filetype($file_name);

            $attachment = array(
                'post_mime_type'    => $file_type['type'],
                'post_title'        => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                'post_content'      => '',
                'post_status'       => 'inherit'
            );

            $attach_id      =   wp_insert_attachment($attachment, $file_loc);
            $attach_data    =   wp_generate_attachment_metadata($attach_id, $file_loc);
            wp_update_attachment_metadata($attach_id, $attach_data);

            $return = array('data' => $attach_data, 'id' => $attach_id);

            return $return;
        }

        return $return;
    }
endif;



if( !function_exists('getHTML') ):
    function getHTML($attachment){
        $attach_id  =   $attachment['id'];
        $file       =   '';
        $html       =   '';

        if( isset($attachment['data']['file'])){
            $file       =   explode('/', $attachment['data']['file']);
            $file       =   array_slice($file, 0, count($file) - 1);
            $path       =   implode('/', $file);

            if(is_page_template('user_dashboard_add.php') ){
                $image      =   $attachment['data']['sizes']['thumbnail']['file'];
            }else{
                $image      =   $attachment['data']['sizes']['user_picture_profile']['file'];
            }

            $dir        =   wp_upload_dir();
            $path       =   $dir['baseurl'] . '/' . $path;
            $html   .=   $path.'/'.$image;
        }

        return $html;
    }
endif;


add_action('wp_ajax_uploadImage64',  'uploadImage64');
add_action('wp_ajax_nopriv_uploadImage64',  'uploadImage64');
   // ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('uploadImage64') ):
function uploadImage64(){
$current_user   =   wp_get_current_user();
$userID  = $current_user->ID;
$timestamp = date("Y-m-d h:i:s");



$heightsAndWidths = array(
    0 => array("width" => 300, "height" => 177),
    1 => array("width" => 1024, "height" => 604),
    2 => array("width" => 150, "height" => 150),
    3 => array("width" => 768, "height" => 453),
    4 => array("width" => 250, "height" => 220),
    5 => array("width" => 255, "height" => 143),
    6 => array("width" => 120, "height" => 120),
    7 => array("width" => 272, "height" => 189),
    8 => array("width" => 1110, "height" => 385),
    9 => array("width" => 143, "height" => 83),
    10 => array("width" => 768, "height" => 662),
    11 => array("width" => 525, "height" => 328),
    12 => array("width" => 980, "height" => 693),
    13 => array("width" => 835, "height" => 467),
    14 => array("width" => 1110, "height" => 623),
    15 => array("width" => 940, "height" => 390),
    16 => array("width" => 105, "height" => 70),
    17 => array("width" => 45, "height" => 45),
    18 => array("width" => 36, "height" => 36)
);

$base64ImageString = $_POST['image'];


$filename= $_POST['name'].$timestamp;

    // Decode the base64 data
    $fileData = base64_decode($base64ImageString);
   


     $uniqueFileName =  $filename . '.jpg'; // Assuming the image is in JPEG format

    // Get the upload directory path (adjust this according to your needs)
    $uploadDir = wp_upload_dir();


    
    // Save the file to the specified upload path
    $filePath = $uploadDir['path'] . '/' . $uniqueFileName;

    if (!is_dir($uploadDir['path'])) {
        wp_mkdir_p($uploadDir['path']);
    }




    // Save the file
    file_put_contents($filePath, $fileData);
$fileURL = $uploadDir['url'] . '/' . $uniqueFileName;


$sourceImage = imagecreatefromstring($fileData);


foreach ($heightsAndWidths as $data) {
        $width = $data['width'];
        $height = $data['height'];
        $imageName = $filename.'-' . $width . 'x' . $height . '.jpg';
        $file_path=$uploadDir['path'] . '/' . $imageName;


 $newImage = imagecreatetruecolor($width, $height);

    // Copy and resize the source image to the new image with the specified dimensions
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $width, $height, imagesx($sourceImage), imagesy($sourceImage));

    imagejpeg($newImage, $file_path);

    // Clean up memory
    imagedestroy($newImage);





//file_put_contents($file_path, $fileData);

}
    // File URL
  
    $exFile = explode("com/",$fileURL);
    $file= "/home/customer/www/acreagesale.com/public_html/$exFile[1]";


    $typeFile = explode(".",$fileURL);
    $type = "image/$typeFile[2]";

    $uploaded_file = array(
        "file" => $file,
        "url" => $fileURL,
        "type" => $type
    );




if (isset($uploaded_file['file'])) {
            $file_loc   =   $uploaded_file['file'];
            $file_name  =   basename($fileURL);
            $file_type  =   wp_check_filetype($file_name);

            $attachment = array(
                'post_mime_type'    => $file_type['type'],
                'post_title'        => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                'post_content'      => '',
                'post_status'       => 'inherit'
            );

     $attach_id      =   wp_insert_attachment($attachment, $file_loc);
$file_ext = $file_type['ext'];

$image_data = file_get_contents($fileURL);
$image_info = getimagesizefromstring($image_data);
  $width = $image_info[0];
    $height = $image_info[1];

 $imageSizeKB = strlen($image_data) / 1024;

$binary_data= round($imageSizeKB, 2);

$file = array(
        'name'      => $filename,
        'type'      => 'image/jpg', // Set the appropriate image type here (e.g., 'image/jpeg' for JPEG images)
        'tmp_name'  => '', // Since this is not a regular file upload, we don't need a temporary file name
        'error'     => 0, // Set the error code to 0, as there's no error
        'size'      => $binary_data, // Calculate the image size from binary data length
        'width'     =>  $width,
        'height'    =>  $height,
        'base'      =>  $base64ImageString, // Store the base64 encoded image for later use if needed
    );


$metadata  = array(
    'width' => 1200,
    'height' => 800,
    'sizes' => array(
        'medium' => array(
            'file' => $filename.'-300x177.'.$file_ext,
            'width' => 300,
            'height' => 177,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 19342,
        ),
        'large' => array(
            'file' => $filename.'-1024x604.'.$file_ext,
            'width' => 1024,
            'height' => 604,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 155700,
        ),
        'thumbnail' => array(
            'file' => $filename.'-150x150.'.$file_ext,
            'width' => 150,
            'height' => 150,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 8940,
        ),
        'medium_large' => array(
            'file' => $filename.'-768x453.'.$file_ext,
            'width' => 768,
            'height' => 453,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 97709,
        ),
        'post-thumbnail' => array(
            'file' => $filename.'-250x220.'.$file_ext,
            'width' => 250,
            'height' => 220,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 19898,
        ),
        'user_picture_profile' => array(
            'file' => $filename.'-255x143.'.$file_ext,
            'width' => 255,
            'height' => 143,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 14230,
        ),
        'agent_picture_thumb' => array(
            'file' => $filename.'-120x120.'.$file_ext,
            'width' => 120,
            'height' => 120,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 6088,
        ),
        'blog_thumb' => array(
            'file' => $filename.'-272x189.'.$file_ext,
            'width' => 272,
            'height' => 189,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 20196,
        ),
        'blog_unit' => array(
            'file' => $filename.'-1110x385.'.$file_ext,
            'width' => 1110,
            'height' => 385,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 65672,
        ),
        'slider_thumb' => array(
            'file' => $filename.'-143x83.'.$file_ext,
            'width' => 143,
            'height' => 83,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 5785,
        ),
        'property_featured_sidebar' => array(
            'file' => $filename.'-768x662.'.$file_ext,
            'width' => 768,
            'height' => 662,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 123571,
        ),
        'property_listings' => array(
            'file' => $filename.'-525x328.'.$file_ext,
            'width' => 525,
            'height' => 328,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 58380,
        ),
        'property_full' => array(
            'file' => $filename.'-980x693.'.$file_ext,
            'width' => 980,
            'height' => 693,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 82163,
        ),
        'listing_full_slider' => array(
            'file' => $filename.'-835x467.'.$file_ext,
            'width' => 835,
            'height' => 467,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 108427,
        ),
        'listing_full_slider_1' => array(
            'file' => $filename.'-1110x623.'.$file_ext,
            'width' => 1110,
            'height' => 623,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 172608,
        ),
        'property_featured' => array(
            'file' => $filename.'-940x390.'.$file_ext,
            'width' => 940,
            'height' => 390,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 82649,
        ),
        'widget_thumb' => array(
            'file' => $filename.'-105x70.'.$file_ext,
            'width' => 105,
            'height' => 70,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 4101,
        ),
        'user_thumb' => array(
            'file' => $filename.'-45x45.'.$file_ext,
            'width' => 45,
            'height' => 45,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 1289,
        ),
		'user_thumb' => array(
            'file' => $filename.'-45x45.'.$file_ext,
            'width' => 36,
            'height' => 36,
            'mime-type' => 'image/'.$file_ext,
            'filesize' => 1289,
        )
    ),
    
    'image_meta' => array(
        'aperture' => 0,
        'credit' => '',
        'camera' => '',
        'caption' => '',
        'created_timestamp' => 0,
        'copyright' => '',
        'focal_length' => 0,
        'iso' => 0,
        'shutter_speed' => 0,
        'title' => '',
        'orientation' => 0,
        'keywords' => array(),
    )

); 
 wp_update_attachment_metadata($attach_id, $metadata);


$arrayReturn = array('linkName'=>$fileURL,'attach_id'=>$attach_id);


 echo json_encode($arrayReturn);

}


}
endif;



add_action('wp_ajax_assignPost',  'assignPost');
if( !function_exists('assignPost') ):
    function assignPost(){

sleep(10);
$args = array(
    'post_type' => 'estate_property',
    'posts_per_page' => 1,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC'
);


 $recent_posts = wp_get_recent_posts($args);
$dataa =$recent_posts[0];
$wpPropName=$dataa['post_title'];

$catchPostIds =json_decode($_POST['attach_id']);
 $propName =$_POST['propName'];

if($wpPropName == $propName)
{
echo $wpPostID =$dataa['ID'];

foreach($catchPostIds as $childID)
{
    echo $childID;

    $post_data = array(
        'ID' => $childID,
        'post_parent' => $wpPostID,
    );
    // Update the post with the new parent post ID
    wp_update_post($post_data);

   // $post_data=array();
}


}
}
endif;


?>
