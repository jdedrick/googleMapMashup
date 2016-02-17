<!DOCTYPE html>
<html>
<head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
   <title>Google Maps JavaScript API v3 Mashup testing</title>
   <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
   <script type="text/javascript" src="./infobox.js"></script>
    <style type="text/css">
     .blah { height:35px;width:34px;background-position: 0 0; border: 3px solid black;}
    </style>
</head>
<body>
  <div id="map" style="width: 1024px; height: 768px;"></div>

  <script type="text/javascript">

  // This is the minimum zoom level that we'll allow
  var minZoomLevel = 4;
  // current zindex for top marker (will increment is a marker with a lower index is clicked))
  var curr_z_index;

  // create the map
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: minZoomLevel,
    center: new google.maps.LatLng(60.020952, -96.240234),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });

  //var canada_layer = new google.maps.KmlLayer('http://localhost/tests/gmaps/canada.kml');
  //canada_layer.setMap(map);


  // Bounds for North America
  var strictBounds = new google.maps.LatLngBounds(
    new google.maps.LatLng(38.891033, -141.767578),
    new google.maps.LatLng(72.100944, -51.064453)
  );

   //// Listen for the dragend event
   //google.maps.event.addListener(map, 'dragend', function() {
   //  if (strictBounds.contains(map.getCenter())) return;

   //  // We're out of bounds - Move the map back within the bounds

   //  var c = map.getCenter(),
   //      x = c.lng(),
   //      y = c.lat(),
   //      maxX = strictBounds.getNorthEast().lng(),
   //      maxY = strictBounds.getNorthEast().lat(),
   //      minX = strictBounds.getSouthWest().lng(),
   //      minY = strictBounds.getSouthWest().lat();

   //  if (x < minX) x = minX;
   //  if (x > maxX) x = maxX;
   //  if (y < minY) y = minY;
   //  if (y > maxY) y = maxY;

   //  map.setCenter(new google.maps.LatLng(y, x));
   //});

    // place multiple markers on map from import

//   var loc_array = new Array();
//   loc_array[0] = new google.maps.LatLng(54.826007999094955, -124.013671875);
//   loc_array[1] = new google.maps.LatLng(56.218923189166624, -114.43359375);
//   loc_array[2] = new google.maps.LatLng(46.98025235521883, -76.9921875);
//   loc_array[3] = new google.maps.LatLng(44.96479793033101, -62.841796875);

//   for (var i = 0; i < loc_array.length; i++) {
//      //alert(loc_array[i].lat() + ' | ' + loc_array[i].lng());
//      addMarker(loc_array[i]);
//  }

  // Limit the zoom level
  google.maps.event.addListener(map, 'zoom_changed', function() {
    if (map.getZoom() < minZoomLevel) map.setZoom(minZoomLevel);
  });

  // create a new node object on map click, in order to fill a new marker properties
  google.maps.event.addListener(map, 'click', function(event) {
    node = new node_obj();
    node.latLng = event.latLng;
    addMarker(node);
  });


  // node object constructor (used to fill marker properties)
  function node_obj() {
    this.latLng;
    this.img;
    this.title;
  }

  /////////////////////////////////
  // create a marker info box (using the InfoBox library))
  var marker_box = document.createElement('div');
  marker_box.style.cssText = 'border: 1px solid black; margin-top: 8px; background: yellow; padding: 5px;';
  marker_box.innerHTML = 'This is test info for the marker';

  var box_options = {
    content: marker_box,
    disableAutoPan: false,
    maxWidth: 0,
    pixelOffset: new google.maps.Size(-140, 0),
    zIndex: null,
    boxStyle: {
      background: 'url("images/tipbox.gif") no-repeat',
      opacity: 0.75,
      width: '280px'
    },
    closeBoxMargin: '10px 2px 2px 2px',
    closeBoxUrl: 'images/close.gif',
    infoBoxClearance: new google.maps.Size(1,1),
    isHidden: false,
    pane: 'floatPane',
    enableEventPropagation: false
  }

  var marker_box = new InfoBox(box_options);
  /////////////////////////////////

  // function for adding a marker to map
  function addMarker(node) {

    if (node.img) {
      var myIcon = new google.maps.MarkerImage(node.img, null, null, null, new google.maps.Size(46,55));
    } else {
      var myIcon = new google.maps.MarkerImage("images/map_icon_cmip.png", null, null, null, new google.maps.Size(46,55));
    }

    var myIcon_shadow = new google.maps.MarkerImage("images/shadow-map_shadow.png", null, null, null, new google.maps.Size(46,55));

    var marker = new google.maps.Marker({
      position: node.latLng,
      map: map,
      //flat: true,
      title: 'My location',
      icon: myIcon,
      shadow: myIcon_shadow,
      draggable: true,
      visible: true
    });

  //google.maps.event.addListener(marker, 'click', function(event) {
  //  this.setMap(null);
  //});

    curr_z_index = curr_z_index + 1;
    marker.setZIndex(curr_z_index);

    google.maps.event.addListener(marker, 'click', function(e) {

      marker_box.open(map, this);
      marker.setAnimation(google.maps.Animation.BOUNCE);
      setTimeout(function(){ marker.setAnimation(null); }, 750);

      if(marker.getZIndex() < curr_z_index) {
        curr_z_index = curr_z_index + 1;
        marker.setZIndex(curr_z_index);
      }

    });
  }

  // populate the map
  function initialize() {

    curr_z_index = 0;

    // get the json feed of node items
    var marker_feed = <?php echo file_get_contents('http://localhost/drupal7/map_feed'); ?>;
    //alert(marker_feed.nodes[0].node.title);

    for (var i=0; i < marker_feed.nodes.length; i++) {

      //alert(marker_feed.nodes[i].node.img);
      var node = new node_obj();
      node.latLng = new google.maps.LatLng(marker_feed.nodes[i].node.Lat, marker_feed.nodes[i].node.Lon),
      node.img = marker_feed.nodes[i].node.icon;
      addMarker(node);
    }
  }
  
   </script>
<body onload="initialize()">

</html>
