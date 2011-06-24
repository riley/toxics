/* 

 An Albers Equal Area Conic projection in javascript, for protovis.

 For centering the contiguous states of the USA in a 800x400 div, I used:

 var scale = pv.Geo.scale(albers(23, -96, 29.5, 45.5))
                .range({ x: -365, y: -375 }, { x: 1200, y: 1200 });

 http://mathworld.wolfram.com/AlbersEqual-AreaConicProjection.html

*/

function albers(lat0, lng0, phi1, phi2) {

    if (lat0 == undefined) lat0 = 23.0;  // Latitude_Of_Origin
    if (lng0 == undefined) lng0 = -96.0; // Central_Meridian
    if (phi1 == undefined) phi1 = 29.5;  // Standard_Parallel_1   29.5 degrees North
    if (phi2 == undefined) phi2 = 45.5;  // Standard_Parallel_2   45.5 degrees North

    lat0 = pv.radians(lat0);
    lng0 = pv.radians(lng0);
    phi1 = pv.radians(phi1);
    phi2 = pv.radians(phi2);

    var n = 0.5 * (Math.sin(phi1) + Math.sin(phi2)),
        c = Math.cos(phi1),
        C = c*c + 2*n*Math.sin(phi1),
        p0 = Math.sqrt(C - 2*n*Math.sin(lat0)) / n;

    return {
        project: function(latlng) {
            var theta = n * (pv.radians(latlng.lng) - lng0),
                p = Math.sqrt(C - 2*n*Math.sin(pv.radians(latlng.lat))) / n;
            return {
                x: p * Math.sin(theta),
                y: p0 - p * Math.cos(theta)
            };
        },
        invert: function(xy) {
            var theta = Math.atan(xy.x / (p0 - xy.y)),
                p = Math.sqrt(xy.x*xy.y + Math.pow(p0 - xy.y, 2));
            return {
                lng: pv.degrees(lon0 + theta/n),
                lat: pv.degrees(Math.asin( (C - p*p*n*n) / (2*n)))
            };
        }
    };
}
