<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<meta name="google-signin-client_id" content="13759604714-0t7p0dh546nvkefuvt58ojmj6dcr82ld.apps.googleusercontent.com">
<meta name="google-signin-scope" content="https://www.googleapis.com/auth/analytics.readonly">
<script src="https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.css" rel="stylesheet" />
<?php
if( !has_site_icon() ){
    print '<link rel="shortcut icon" href="'.get_theme_file_uri('/img/favicon.gif').'" type="image/x-icon" />';
}
wp_head();?>
</head>

<body <?php body_class(); ?>>

<?php
if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
} else {
    do_action( 'wp_body_open' );
}

$logo_header_type   =   wpresidence_get_option('wp_estate_logo_header_type','');
$header_classes     =   wpestate_header_classes();
if( $logo_header_type=='type3' &&  wpestate_is_user_dashboard() ){
  $logo_header_type='type1';
}
get_template_part('templates/mobile_menu' );
?>

<div class="website-wrapper" id="all_wrapper" >
  <div class="container main_wrapper <?php echo esc_attr($header_classes['main_wrapper_class']) ;?>">

  <?php
  $is_elementor_in_use='header_media_elementor';
  if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
    wpresidence_show_header_wrapper($header_classes,$logo_header_type);
      $is_elementor_in_use='header_media_non_elementor';
  }

  get_template_part( 'header_media','', array(
        'elementor_class'   => $is_elementor_in_use,
    ) );

        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;






  
  ?>
<input type="hidden" id="user-ID" value="<?php echo $userID; ?>">
  <div class="pre_search_wrapper"></div>
<div class="container content_wrapper">





<script>

jQuery(document).ready(function () {

jQuery('#form_submit_1').on('click', function(event) {


    var propName = jQuery("#title").val();

    //  event.preventDefault()
 var uploadedImages = jQuery('#imagelist .uploaded_images');

  // Create an array to store the 'data-imageid' values
  var imageIds = [];

  // Loop through each 'uploaded_images' element and extract the 'data-imageid'
  uploadedImages.each(function() {
    var imageId = jQuery(this).data('imageid');
    imageIds.push(imageId);
  });

  // Convert the array to a JSON string
  var imageIdsJSON = JSON.stringify(imageIds);

   var valuesAll = imageIds.toString();

jQuery("#attachid").val(","+valuesAll);


  // Store the JSON string in local storage with a key, e.g., 'imageIds'
  localStorage.setItem('imageIds', imageIdsJSON);


 jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
              action: "assignPost",
              propName: propName,
              attach_id: imageIdsJSON
              
            },
            success: function (response) {
            
       

            }
          });


});

  if (window.location.toString().includes("add-a-listing")) {




    jQuery(".submitrow").append(
      '<div id="frontFace"></div><div id="dimen"></div><div id="north"></div><div id="south"></div><div id="east"></div><div id="west"></div>'
    );

    var intervalId;

  
      intervalId = setInterval(function () {
        var textFieldValue = jQuery("#property_latitude").val();

        if (textFieldValue !== "") {
          // Text field is not empty, stop the interval
          clearInterval(intervalId);
          // Enable the button to execute further code

          var lat = jQuery("#property_latitude").val();
          var longi = jQuery("#property_longitude").val();
          var adressFields = jQuery("#property_city_submit").val();

           



          var myHeaders = new Headers();
          myHeaders.append("Accept", "application/json");
          myHeaders.append(
            "x-regrid-token",
            "eyJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJyZWdyaWQuY29tIiwiaWF0IjoxNjkxNTE2Mjg0LCJleHAiOjE2OTQxMDgyODQsInUiOjMwNDAzMSwiZyI6MjMxNTMsImNhcCI6InBhOnRzOnBzOmJmOm1hOnR5OmVvOnNiIn0.4kr-yZBgWFpmxkMIno7IEYr7FCZW6bLVVH4AZ20Xs84"
          );
          myHeaders.append(
            "Cookie",
            "_session_id=5c9ac973b507b3c124bc938c2c4239a9"
          );

          var requestOptions = {
            method: "GET",
            headers: myHeaders,
            redirect: "follow",
          };
          const latVal = [];
          fetch(
            "https://app.regrid.com/api/v1/search.json?lat=" +
              lat +
              "&lon=" +
              longi,
            requestOptions
          )
            .then((response) => response.json())
            .then((result) => {


           
              const latArray = result.results[0].geometry.coordinates[0];
              jQuery.each(latArray, function (index, item) {
                latVal.push("[" + item[0] + "," + item[1] + "]");
              });

              const address = result.results[0].properties.addresses[0];
              const city = address.a_scity;
              const zip = address.a_szip;
              const state = address.a_state2;
              const country = address.a_county;
              const neighborhood = address.neighborhood;
              const centerLat = address.a_lat;
              const centerLong = address.a_lon;
             

              const polygonCoordinates = JSON.stringify(latVal);
           

              var acreLandd = result.results[0].properties.fields.ll_gisacre;
               var acreLand = Math.floor(acreLandd * 100) / 100;
              

              var acreAreaa = result.results[0].properties.fields.impr_area_size;
                var acreArea = Math.floor(acreAreaa * 100) / 100;


              const areaAPN = result.results[0].properties.fields.parcelnumb;

  const zoning = result.results[0].properties.fields.zoning;
  const usedesc = result.results[0].properties.fields.usedesc;

  var reqText= "create description to sell this Property in "+city+ ","+country+" | Size: "+acreLand+" | Ideal for  "+usedesc+" | zoning is "+zoning+" Varies by governing municipality";
  var reqTextTitle= "Create title without without  inverted commas for this Property in "+city+ ","+country+" | Size: "+acreLand+" | Ideal for  "+usedesc;


//code for description
var myHeaders = new Headers();
myHeaders.append("Authorization", "Bearer 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");
myHeaders.append("Content-Type", "application/json");

var raw = JSON.stringify({
  "model": "gpt-3.5-turbo",
  "messages": [
    {
      "role": "user",
      "content": reqText
    }
  ]
});

var requestOptions = {
  method: 'POST',
  headers: myHeaders,
  body: raw,
  redirect: 'follow'
};

fetch("https://api.openai.com/v1/chat/completions", requestOptions)
  .then(response => response.text())
  .then( function(res){
      const allData = JSON.parse(res);

      const messageGpt = allData.choices[0].message.content;
 


jQuery("#description").val(messageGpt);
 } );




var rawtitle = JSON.stringify({
  "model": "gpt-3.5-turbo",
  "messages": [
    {
      "role": "user",
      "content": reqTextTitle
    }
  ]
});

var requestOptions = {
  method: 'POST',
  headers: myHeaders,
  body: rawtitle,
  redirect: 'follow'
};

fetch("https://api.openai.com/v1/chat/completions", requestOptions)
  .then(response => response.text())
  .then( function(res){
      const allData = JSON.parse(res);

      const gptTitle = allData.choices[0].message.content;
 


jQuery("#title").val(gptTitle);
 } );

//

              jQuery("#property_lot_size").val(acreLand);
              jQuery("#county").val(country);
              jQuery("#city").val(city);
              jQuery("#property_size").val(acreArea);
              jQuery("#apn-number").val(areaAPN);
              jQuery("#gps-coordonates").val(lat + ", " + longi);
             
 
var sensitivity = 0.05; // Adjust the sensitivity as needed
var zoomVal;

if (acreLand < 0.05) {
  zoomVal = 19;
} else {
  zoomVal = 19 - Math.floor((acreLand - 0.05) / sensitivity) * sensitivity;
}

// Ensure minimum value of 16
if (zoomVal < 16) {
  zoomVal = 16;
}





              mapboxgl.accessToken =
                "pk.eyJ1IjoicmFpY2E5OCIsImEiOiJja3dvNjVkMXMwN2gxMm5zMXFibzNtODJ4In0.tY-jHrvT4lj3VawMobrdOA";
                  // Calculate distance between two points


                  function getDistance(coords1, coords2) {
                    const [lon1, lat1] = coords1;
                    const [lon2, lat2] = coords2;
                    const R = 6371e3; // Earth's radius in meters
                    const φ1 = lat1 * (Math.PI / 180);
                    const φ2 = lat2 * (Math.PI / 180);
                    const Δφ = (lat2 - lat1) * (Math.PI / 180);
                    const Δλ = (lon2 - lon1) * (Math.PI / 180);

                    const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                      Math.cos(φ1) * Math.cos(φ2) *
                      Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                    const distance = R * c;
                    return distance;
                  }

                  // Calculate the midpoint between two points
                  function getMidpoint(coords1, coords2) {
                    const [lon1, lat1] = coords1;
                    const [lon2, lat2] = coords2;
                    const lonMid = (lon1 + lon2) / 2;
                    const latMid = (lat1 + lat2) / 2;
                    return [lonMid, latMid];
                  }

                  // Convert meters to feet
                  function metersToFeet(meters) {
                    return meters * 3.28084;
                  }

              function initMap(values) {

                
                var renderbtn = values.rendorID;
                var bearing = values.bearing;
                var container = values.container;
               var front = values.front;



                // Set the coordinates for your polygon
                var polygonCoordinates = latArray;

              
                //first View
                var aa = centerLat;
                var bb = centerLong;

                if(front === "frontFace"){
                var  zoomVall = zoomVal + 0.5; 
                  
                // Create a map NORTH 1st
                var mapNorth = new mapboxgl.Map({
                container: container,
                pitch: 45,
                bearing: bearing,
                style: "mapbox://styles/mapbox/satellite-streets-v12",
                center: [bb, aa],
                zoom: zoomVall,
                });

                var rectangleGeoJSON = {
                "type": "Feature",
                "geometry": {
                "type": "Polygon",
                coordinates: [polygonCoordinates]
                },
                "properties": {
                "height": 10 // Adjust this value to set the height of the rectangle in meters
                }
                };

                mapNorth.on('load', function () {
                mapNorth.addSource('3d-rectangle-source', {
                type: 'geojson',
                data: rectangleGeoJSON
                });

                mapNorth.addLayer({
                id: '3d-rectangle',
                type: 'fill-extrusion',
                source: '3d-rectangle-source',
                paint: {
                'fill-extrusion-color': '#209c2c', // Set the color of the rectangle
                'fill-extrusion-height': ['get', 'height'],
                'fill-extrusion-base': 0,
                'fill-extrusion-opacity': 0.6 // Set the opacity of the rectangle
                }
                });
                });

/////////////////////////////////////////////////////////////////////
              }else if(front === "dimen")
              {


                 // Create a map NORTH 1st
                var mapNorth = new mapboxgl.Map({
                container: container,
                pitch: 45,
                bearing: bearing,
                style: "mapbox://styles/mapbox/satellite-v9",
                center: [bb, aa],
                zoom: zoomVal,
                });

                var rectangleGeoJSON = {
                "type": "Feature",
                "geometry": {
                "type": "Polygon",
                coordinates: [polygonCoordinates]
                },
                "properties": {
                "height": 10 // Adjust this value to set the height of the rectangle in meters
                }
                };

                mapNorth.on('load', function () {
                mapNorth.addSource('3d-rectangle-source', {
                type: 'geojson',
                data: rectangleGeoJSON
                });

                mapNorth.addLayer({
                id: '3d-rectangle',
                 type: 'line',
                source: '3d-rectangle-source',
                paint: {
                  'line-color': '#ff5722', // Set the color of the outlines to a vibrant orange
                  'line-width': 8 // Set the width of the outlines to four times the previous value
                }
                });

                  var coordinates = rectangleGeoJSON.geometry.coordinates[0];
                  console.log("coordinates",coordinates);

                  for (var i = 0; i < coordinates.length - 1; i++) {
                    var point1 = coordinates[i];
                    console.log("point1",point1);

                    var point2 = coordinates[i + 1];
                    console.log("point2",point2);

                    var distanceMeters = getDistance(point1, point2);
                    console.log("distanceMeters",distanceMeters);

                    var distanceFeet = metersToFeet(distanceMeters);
                    console.log("distanceFeet",distanceFeet);

                    var midpoint = getMidpoint(point1, point2);
                    console.log("midpoint",midpoint);


                    var popup = new mapboxgl.Popup({
                      closeButton: false,
                      closeOnClick: false,
                      anchor: 'center', // Show the popup in the center of the line
                    })
                      .setLngLat(midpoint)
                      .setHTML(`<div class="popup-content">${distanceFeet.toFixed(2)} feet</div>`)
                      .addTo(mapNorth);

                    // Set the popup to fully transparent
                    popup.getElement().style.background = 'transparent';
                    popup.getElement().style.boxShadow = 'none';
                  }


                });
              }
              //////////////////////////////////////////
              else
              {
                                var mapNorth = new mapboxgl.Map({
                  container: container,
                  pitch: 60,
                  bearing: bearing,
                  style: "mapbox://styles/mapbox/satellite-streets-v12",
                  center: [bb, aa],
                  zoom: zoomVal,
                });

                // Add the polygon to the map
                mapNorth.on("load", function () {
                  mapNorth.addLayer({
                    id: "polygon",
                    type: "fill",
                    source: {
                      type: "geojson",
                      data: {
                        type: "Feature",
                        geometry: {
                          type: "Polygon",
                          coordinates: [polygonCoordinates],
                        },
                      },
                    },
                    paint: {
                      "fill-color": "#209c2c",
                      "fill-outline-color": "#FFFF00",
                      "fill-opacity": 0.55,
                    },
                  });
                });

              }


               setTimeout(function () {
                mapNorth.once("render", function () {    
                mapNorth.getCanvas().toBlob(function (blob) {
                    // Create a temporary link to download the screenshot
                    var link = document.createElement("a");
                    link.href = URL.createObjectURL(blob);

                  

                     fetch(link.href)
                    .then(response => response.blob())
                    .then(blob => {
                     
                        const reader = new FileReader();
                        reader.onload = function(event) {

                        const base64Image = event.target.result;

                          

                          // Remove the prefix "data:image/png;base64," using replace() method with regex
                          let base64Data = base64Image.replace(
                            /^data:image\/png;base64,/,
                            ""
                          );

                          

                          var activeUserID = jQuery("#user-ID").val();

                          jQuery.ajax({
                            type: "POST",
                            url: ajaxurl,
                            data: {
                              action: "uploadImage64",
                              attach_id: activeUserID,
                              name: container,
                              image: base64Data,
                            },
                            success: function (response) {
                              const dataResponse = response.replace(/0$/, "");
                              var valueData = JSON.parse(dataResponse);
                              var attach_id = valueData.attach_id;
                              var linkName = valueData.linkName;
                              var linkNameAdjusted =
                                linkName.replace(".jpg", "") + "-255x143.jpg";

                               jQuery(".ui-sortable").append(
                            '<div class="uploaded_images" data-store="custom" data-imageid="' +
                            attach_id +
                            '"  data-image="' +
                            attach_id +
                            '"   ><img src="' +
                            linkNameAdjusted +
                            '"><i class="jsdelete deleter far fa-trash-alt"></i><i class="fas fa-font image_caption_button"></i><div class="image_caption_wrapper"><input data-imageid="' +attach_id + '"type="text" class="image_caption form_control" name="image_caption" value=""></div></div>'
                        );
                            },
                          });
                        };

                        // Read the Blob as data URL (which will give you the base64-encoded string)
            reader.readAsDataURL(blob);
          });

        link.download = "mapNorth.png";
      });
  
  });

                mapNorth.repaint = true;

                  },18000); // 15-second delay

                  jQuery("#"+container).hide();
              }

              const north = {
                rendorID: "captureButtonNorth",
                bearing: 60,
                container: "north",
              };

              const south = {
                rendorID: "captureButtonSouth",
                bearing: 150,
                container: "south",
              };

              const east = {
                rendorID: "captureButtonEast",
                bearing: 240,
                container: "east",
              };

              const west = {
                rendorID: "captureButtonWest",
                bearing: 330,
                container: "west",
              };
              const northFace = {
                rendorID: "captureButtonNorthFace",
                bearing: 0,
                container: "frontFace",
                front: "frontFace",
              };
              const dimen = {
                rendorID: "dimention",
                bearing: 0,
                container: "dimen",
                front: "dimen",
              };


              initMap(northFace);

              
              
              setTimeout(function () {
			        initMap(north);
              initMap(south);
              initMap(east);
              initMap(west);
              initMap(dimen);
              },5000);
            });



//Code for the Places 

 fetch(
  "https://api.geoapify.com/v2/places?categories=commercial.supermarket,commercial.shopping_mall,commercial.department_store,catering.restaurant,entertainment.theme_park,education.university,sport.stadium,adult.casino,camping,airport&filter=circle:" +
    longi +
    "," +
    lat +
    ",16093&limit=15&apiKey=4f5d366758c044f4a87200c9e09e7807"
)
  .then((response) => response.json())
  .then((data) => {
    const loc = [];
    const loc_names = [];

    if (data.features && data.features.length > 0) {
      data.features.forEach((result) => {
        const geometrylat = result.properties.lat;
        const geometrylng = result.properties.lon;
        loc.push({
          lat: geometrylat,
          lng: geometrylng,
        });
        const cityName = result.properties.name;
        loc_names.push(cityName);
      });

      let mapUrl = "https://maps.geoapify.com/v1/staticmap";
      mapUrl +=
        "?width=900&height=700&zoom=14.4&bearing=7&apiKey=4f5d366758c044f4a87200c9e09e7807&marker=";

      // Add markers for each place
      let i = 1;
      loc.forEach((place, index) => {
        let marker = "";
        if (i === 1) {
          marker =
            "type:awesome;color:%2319b8fc;text:" +
            i +
            ";size:large;lonlat:" +
            place.lng +
            "," +
            place.lat;
        } else {
          marker =
            "|type:awesome;color:%2319b8fc;text:" +
            i +
            ";size:large;lonlat:" +
            place.lng +
            "," +
            place.lat;
        }
        i++;
        mapUrl += marker;
      });



      jQuery(".submitrow").append(
        '<div class="both" style="display: flex; justify-content: space-around;"><div class="image-container"><img id="original-image" crossorigin="anonymous" src="' +
          mapUrl +
          '" alt="Original Image"> <canvas id="canvas"></canvas></div>'
      );

      setTimeout(function() {

      var listItems = loc_names;

      var title = "  Things to do Near: " + adressFields;
      var canvas = document.getElementById("canvas");
      var image = document.getElementById("original-image");
      canvas.width = image.width + 300;
      canvas.height = image.height;
      var ctx = canvas.getContext("2d");
      ctx.drawImage(image, 0, 0);
      ctx.fillStyle = "white";
      ctx.font = "16px Arial";
      var x = 900;
      var y = 50;
      var line_height = 40;
      ctx.fillStyle = "#93C493";
      ctx.fillRect(x, y - 100, 300, line_height * listItems.length + 300);
      ctx.fillStyle = "#fff";
      ctx.fillText(title, x, y);
      listItems.forEach((item, index) => {
        var inVal = index + 1;
        var valueItem = "   " + inVal + " " + item;
        ctx.fillText(valueItem, x, y + 40);
        y += line_height;
      });

      var mapImage = new Image();
      mapImage.crossOrigin = "Anonymous";
      mapImage.onload = function () {
        ctx.drawImage(mapImage, 0, 0);

        var activeUserID = jQuery("#user-ID").val();
        var img = canvas.toDataURL("image/png");
        let base64Data = img.replace(/^data:image\/png;base64,/, "");


        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "uploadImage64",
            attach_id: activeUserID,
            name: "map_places",
            image: base64Data,
          },
          success: function (response) {
            const dataResponse = response.replace(/0$/, "");
            var valueData = JSON.parse(dataResponse);
            var attach_id = valueData.attach_id;
            var linkName = valueData.linkName;
            var linkNameAdjusted =
              linkName.replace(".jpg", "") + "-255x143.jpg";

            jQuery(".ui-sortable").append(
              '<div class="uploaded_images" data-store="custom" data-imageid="' +
                attach_id +
                '"  data-image="' +
                attach_id +
                '"   ><img src="' +
                linkNameAdjusted +
                '"><i class="jsdelete deleter far fa-trash-alt"></i><i class="fas fa-font image_caption_button"></i><div class="image_caption_wrapper"><input data-imageid="' +
                attach_id +
                '"type="text" class="image_caption form_control" name="image_caption" value=""></div></div>'
            );
            jQuery(".image-container").hide();
            jQuery(".jsdelete").on("click", function () {
              jQuery(this).parent().remove();
            });
          },
        });
      };
      mapImage.src = mapUrl;
        }, 30000);
    }
  });

//Code for cities 

 fetch(
  "https://api.geoapify.com/v2/places?categories=populated_place.city&filter=circle:"+longi +"," +lat +",80000&limit=20&apiKey=4f5d366758c044f4a87200c9e09e7807"
)
  .then((response) => response.json())
  .then((data) => {
    const cityLoc = [];
    const city_names = [];
    if (data.features && data.features.length > 0) {
      data.features.forEach((result) => {

        const geometrylat = result.geometry.coordinates[1];
        const geometrylng = result.geometry.coordinates[0];
        cityLoc.push({
          lat: geometrylat,
          lng: geometrylng,
        });
        const cityName = result.properties.city;
        city_names.push(cityName);
      });

      let mapCityUrl = "https://maps.geoapify.com/v1/staticmap";
      mapCityUrl +=
        "?width=900&height=700&zoom=14.4&bearing=7&apiKey=4f5d366758c044f4a87200c9e09e7807&marker=";

      let i = 1;
      cityLoc.forEach((place, index) => {
        let marker = "";
        if (i === 1) {
          marker =
            "type:awesome;color:red;text:" +
            i +
            ";size:large;lonlat:" +
            place.lng +
            "," +
            place.lat;
        } else {
          marker =
            "|type:awesome;color:red;text:" +
            i +
            ";size:large;lonlat:" +
            place.lng +
            "," +
            place.lat;
        }
        i++;
        mapCityUrl += marker;
      });

      jQuery(".submitrow").append(
        '<div class="image-container" id="second"><img id="original-image-city" crossorigin="anonymous" src="' +
          mapCityUrl +
          '" alt="Original Image"><canvas id="canvass"></canvas></div></div>'
      );

      setTimeout(function () {
        var listItemsCity = city_names;

        var cityTitle = "  Cities Near: "+adressFields;

        var canvas = document.getElementById("canvass");
        var image = document.getElementById("original-image-city");
        canvas.width = image.width + 300;
        canvas.height = image.height;
        var ctx = canvas.getContext("2d");
        ctx.drawImage(image, 0, 0);
        ctx.fillStyle = "white";
        ctx.font = "16px Arial";
        var x = 900;
        var y = 50;
        var line_height = 50;
        ctx.fillStyle = "#93C493";
        ctx.fillRect(x, y - 100, 300, line_height * listItemsCity.length + 300);
        ctx.fillStyle = "#fff";

        ctx.fillText(cityTitle, x, y);

        listItemsCity.forEach((item, index) => {
          var inVal = index + 1;
          var valueItem = "   " + inVal + " " + item;
          ctx.fillText(valueItem, x, y + 40);
          y += line_height;
        });

        var mapImage = new Image();
        mapImage.crossOrigin = "Anonymous";
        mapImage.onload = function () {
          ctx.drawImage(mapImage, 0, 0);

          var activeUserID = jQuery("#user-ID").val();
          var img = canvas.toDataURL("image/png");
          let base64Data = img.replace(/^data:image\/png;base64,/, "");

          jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
              action: "uploadImage64",
              attach_id: activeUserID,
              name: "nearCitites",
              image: base64Data,
            },
            success: function (response) {
              const dataResponse = response.replace(/0$/, "");
              var valueData = JSON.parse(dataResponse);
              var attach_id = valueData.attach_id;
              var linkName = valueData.linkName;
              var linkNameAdjusted =
                linkName.replace(".jpg", "") + "-255x143.jpg";

              jQuery(".ui-sortable").append(
                '<div class="uploaded_images" data-store="custom" data-imageid="' +
                  attach_id +
                  '"  data-image="' +
                  attach_id +
                  '"   ><img src="' +
                  linkNameAdjusted +
                  '"><i class="jsdelete deleter far fa-trash-alt"></i><i class="fas fa-font image_caption_button"></i><div class="image_caption_wrapper"><input data-imageid="' +
                  attach_id +
                  '"type="text" class="image_caption form_control" name="image_caption" value=""></div></div>'
              );
              jQuery(".image-container").hide();
              jQuery(".jsdelete").on("click", function () {
                jQuery(this).parent().remove();
              });
            },
          }); 
        };
        mapImage.src = mapCityUrl;
      }, 30000);
    }
  });




        }
      }, 9000);
    
  }
});

</script>     




          
        

               
          