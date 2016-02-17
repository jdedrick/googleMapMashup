// populate the map
(function() {

  curr_z_index = 0;

  // get the json feed of node items
  // each node in the feed will (should) include: title, icon image location, lat, long, and a path to the node
  // for this particalr project, icons are dynamically created from a drupal api that uses imagecache to splice an image onto an icon template background

  //for the purposes of testing, the data has been hardcoded but yes, this would be done dynamically through a get method
  var feedData = data;

  // loop through all the fetched data to populate the map with predefined markers
  for (var i=0; i < data.nodes.length; i++) {

    var node = nodeObj.newNodeObj();
    node.latLng = new google.maps.LatLng(feedData.nodes[i].node.Lat, feedData.nodes[i].node.Lon),
    node.title = feedData.nodes[i].node.title;
    node.img = feedData.nodes[i].node.icon;
    var styles = 'border: 1px solid black; margin-top: 8px; background: yellow; padding: 5px;';

    // add the icon to the map
    mapIcon.addMarker(node, styles);
  }
})();
