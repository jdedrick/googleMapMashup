var googleMap = (function googleMap(){
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

  // Limit the zoom level
  google.maps.event.addListener(map, 'zoom_changed', function() {
    if (map.getZoom() < minZoomLevel) map.setZoom(minZoomLevel);
  });

  // create a new node object on map click, in order to fill a new marker properties
  google.maps.event.addListener(map, 'click', function(event) {
    node = nodeObj.newNodeObj();
    node.latLng = event.latLng;
    var styles = 'border: 1px solid black; margin-top: 8px; background: orange; padding: 5px;';
    mapIcon.addMarker(node, styles);
  });

  return {
    getMap: function getMap() {
      return map;
    }
  }
})();

// node object constructor (used to fill marker properties)
var nodeObj = (function() {
  function node_obj() {
    this.latLng;
    this.img;
    this.title;
  }

  return {
    newNodeObj: function newNodeObj() {
      var node = new node_obj();
      return node;
    }
  }
})();

// create a marker info box (using the InfoBox library))
var markerBox = (function markerBox() {
  function createMarkerBox(content, style) {
    var box = document.createElement('div');
     box.style.cssText = style;
     box.innerHTML = content;

    var box_options = {
      content: box,
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

    var box = new InfoBox(box_options);

    return box;
  }

  return {
    createMarkerBox: createMarkerBox
  }
})();

// create a new map icon
// inject the markerbox for creating the info box related to the marker
var mapIcon = (function mapIcon(markerBox) {

  var map = googleMap.getMap();
  // function for adding a marker to map
  function addMarker(node, styles) {

    // create a new instance of a google Marker image and populate it (image) depending on whether it's new or loaded
    var newIcon;
    if (node.img) {
      newIcon = new google.maps.MarkerImage(node.img, null, null, null, new google.maps.Size(46,55));
    } else {
      newIcon = new google.maps.MarkerImage("images/map-icon-mapleleaf.png", null, null, null, new google.maps.Size(46,55));
    }

    var title = (node.title) ? node.title : 'New icon!';

    var newIcon_shadow = new google.maps.MarkerImage("images/shadow-map_shadow.png", null, null, null, new google.maps.Size(46,55));

    var marker = new google.maps.Marker({
      position: node.latLng,
      map: map,
      //flat: true,
      title: title,
      icon: newIcon,
      shadow: newIcon_shadow,
      draggable: true,
      visible: true
    });

    curr_z_index = curr_z_index + 1;
    marker.setZIndex(curr_z_index);

    // add an map event listener to add a new marker to the map on each click
    google.maps.event.addListener(marker, 'click', function(e) {
      var newMarkerBox = markerBox.createMarkerBox(title, styles);
      newMarkerBox.open(map, this);
      marker.setAnimation(google.maps.Animation.BOUNCE);
      setTimeout(function(){ marker.setAnimation(null); }, 750);

      if(marker.getZIndex() < curr_z_index) {
        curr_z_index = curr_z_index + 1;
        marker.setZIndex(curr_z_index);
      }

    });
  }

  return {
    addMarker: addMarker
  }

})(markerBox);
