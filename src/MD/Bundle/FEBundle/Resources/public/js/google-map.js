function initialize() {
    var myLatlng = new google.maps.LatLng(40.6845705, -74.1856747);
    var mapCanvas = document.getElementById('google-map-canvas');
    var mapOptions = {
        center: myLatlng,
        zoom: 11,
        zoomControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL
        },
        panControl: false,
        mapTypeControl: true,
        scrollwheel: false
    };
    var map = new google.maps.Map(mapCanvas, mapOptions);
}
google.maps.event.addDomListener(window, 'load', initialize);
