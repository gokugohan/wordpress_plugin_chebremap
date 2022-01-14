$(document).ready(function(){
    $('.sidenav').sidenav();
    $('.modal').modal();

    let map, panelInfo, redCircleOption = {color: "#f00000", opacity: 0.85},
        greenCircleOption = {color: "#00ff00", opacity: 0.3};
    let overlayLayerName = "Layers";
    let iconUrl = $("#iconUrl").val();
    var markers_poi = L.markerClusterGroup();

    let osmBasemap = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    });

    let imagery = L.tileLayer(
        'http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            attribution: 'Tiles &copy; <a href="https://www.esri.com">Esri</a> &mdash; Source: <a href="http://server.arcgisonline.com/arcgis/rest/services/World_Imagery/MapServer" target="_blank">Esri World_Imagery</a>'
        });
    let topographic = L.esri.basemapLayer('Topographic');
    let darkGray = L.esri.basemapLayer('DarkGray');


    map = L.map("map", {
        center: [-8.787519, 125.946401],
        zoom: 9,
        layers: [osmBasemap],
        zoomControl: false,
        scrollWheelZoom: false,
        attributionControl: false,
    });


    $("#map").contextmenu(function (ev) {
        return false;
    })

    var geojson = {
        // "name": "point_of_interests",
        "type": "FeatureCollection",
        "features": []
    };

    point_of_interests_data.each(function(key,val){
        console.log(key,val);
    })


    var baseLayers = {
        "Default Base Map": osmBasemap,
        "Topographic Map": topographic,
        "Sattelite Imagery": imagery,
        "DarkGray": darkGray
    };

    var groupedOverlays = {
        "Layers": {
            "Point of interests": markers_poi,
        }
    };

    var layerControl = L.control.groupedLayers(baseLayers, groupedOverlays, {collapsed: false}).addTo(map);


    $(".btn-location-coordenate").on("click", function () {
        let lat = $(this).data("lat");
        let lng = $(this).data("lng");
        let title = $(this).data("title");
        map.flyTo([lat, lng], 15);
    });


    /* Layer control listeners that allow for a single markerClusters layer */
    map.on("overlayadd", function (e) {
        if (e.layer === markers_poi) {
            console.log("add markers_poi");
            $(".poi-search-item").show();
        }
    });


    map.on("overlayremove", function (e) {
        if (e.layer === markers_poi) {
            $(".poi-search-item").hide();
        }
    });



    $("#form-upload-geojson").on('submit', (event) => {
        event.preventDefault();

        var file = $("#input-upload-file").prop('files')[0];

        handleUploadedFile(file);

        $("#modal_load_file").modal("close");

    });

    let dropbox = document.getElementById("map");
    dropbox.addEventListener("dragenter", function (e) {
        e.stopPropagation();
        e.preventDefault();
        map.scrollWheelZoom.disable();
    }, false);
    dropbox.addEventListener("dragover", function (e) {
        e.stopPropagation();
        e.preventDefault();
    }, false);
    dropbox.addEventListener("drop", function (e) {
        e.stopPropagation();
        e.preventDefault();
        map.scrollWheelZoom.enable();
        var dt = e.dataTransfer;
        var files = dt.files;

        var i = 0;
        var len = files.length;
        if (!len) {
            return
        }
        while (i < len) {
            handleUploadedFile(files[i]);
            i++;
        }
    }, false);
    dropbox.addEventListener("dragleave", function () {
        map.scrollWheelZoom.enable();
    }, false);


    function handleUploadedFile(file) {
        let file_extension = file.name.split('.')[1];

        map.spin(true, {lines: 13, length: 40});

        if (file_extension === 'zip') {
            return handleZipFile(file);
        }

        let reader = new FileReader();
        reader.onload = function () {
            let fileUrl = window.URL.createObjectURL(file);
            if (reader.readyState !== 2 || reader.error) {
                return;
            }

            switch (file_extension) {
                case "geojson":
                    $.getJSON(fileUrl, function (data) {
                        loadUploadedGeoJsonFile(data, file.name, overlayLayerName);
                        map.spin(false);
                    });
                    break;
                case "topojson":
                    loadTopoJson(fileUrl, file.name);
                    map.spin(false);
                    break;

                case "csv":
                    loadCSV(fileUrl, file.name);
                    map.spin(false);
                    break;
                case "kml":
                    loadKML(fileUrl, file.name);
                    map.spin(false);
                    break;
                default:
                    M.toast({
                        html: 'We only accept file GEOJSON, TOPOJSON, CSV, KML, or Zipped Shapefile!',
                        classes: 'rounded red darken-1'
                    });
                    map.spin(false);
            }


        };
        reader.readAsArrayBuffer(file);
    }


    function handleZipFile(file) {

        var reader = new FileReader();
        reader.onload = function () {
            loadZipShp(this.result, file.name);
        };
        reader.readAsArrayBuffer(file);
    } //handleZipFile


    function loadZipShp(bufferFile, filename) {
        var zipPointMarkerClusters = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            disableClusteringAtZoom: 16
        });

        // let geometry_type='';
        let shpFile = new L.Shapefile(bufferFile, {
            style: function (feature) {
                return {
                    color: "#960707",
                    fillOpacity: 1,
                    stroke: "#000",
                    weight: 2,
                    dashArray: '3',
                    fillColor: colorbrewer.Spectral[11][
                    Math.abs(JSON.stringify(feature).split("").reduce(
                        function (a, b) {
                            a = ((a << 5) - a) + b.charCodeAt(0);
                            return a & a
                        }, 0)) % 11]
                };
            },

            onEachFeature: function (feature, layer) {
                console.log(feature.geometry.type);
                geometry_type = feature.geometry.type;
                if (feature.properties) {
                    layer.bindPopup(Object.keys(feature.properties).map(function (k) {
                        return k + ": " + feature.properties[k];
                    }).join("<br />"), {
                        maxHeight: 200
                    });
                }
            }
        });

        // console.log("geometry_type: " + geometry_type);
        // if (geometry_type == "Point") {
        //     zipPointMarkerClusters.addLayer(shpFile);
        //     zipPointMarkerClusters.addTo(map);
        // } else {
        //     shpFile.addTo(map);
        // }


        shpFile.once('data:loaded', function () {
            map.spin(false);
            zipPointMarkerClusters.addLayer(shpFile);
            zipPointMarkerClusters.addTo(map);

            layerControl.addOverlay(zipPointMarkerClusters, filename, overlayLayerName);
        })
    } //loadZipShp


    function loadTopoJson(url, filename) {
        let geometry_type;

        let topoMarkerClusters = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            disableClusteringAtZoom: 16
        });

        let topoLayer = L.topoJson(null, {
            style: function (feature) {
                return {
                    color: "#000",
                    opacity: 1,
                    weight: 1,
                    fillColor: '#35495d',
                    fillOpacity: 0.8
                }
            },
            onEachFeature: function (feature, layer) {

                console.log(feature);
                geometry_type = feature.geometry.type;
                var properties = layer.feature.properties;

                var column = Object.keys(properties);
                var html = "<ul class='list-group'>";

                for (var i = 0; i < column.length; i++) {

                    $.each(properties, function (key, value) {
                        html += "<li class='list-group-item'>" + key + " - " + value + "</li>";
                    });
                    break;
                }

                html += "</ul>";
                layer.bindPopup(html);
            }
        });

        addTopoData(url).then(data => {
            topoLayer.addData(data);

            topoLayer.StyledLayerControl = {
                removable: true,
                visible: false
            }

            map.flyToBounds(topoLayer.getBounds());

            if (geometry_type == "Point") {
                topoMarkerClusters.addLayer(topoLayer);
                topoMarkerClusters.addTo(map);
                layerControl.addOverlay(topoMarkerClusters, filename, overlayLayerName);
            } else {
                layerControl.addOverlay(topoMarkerClusters, filename, overlayLayerName);
            }

            M.toast({
                html: 'File Added',
                className: 'rounded green accent-3'
            });

            map.spin(false);
        });


    }// loadTopoJson


    function loadCSV(url, filename) {
        // Read markers data from data.csv
        $.get(url, function (csvString) {

            // Use PapaParse to convert string to array of objects
            let data = Papa.parse(csvString, {header: true, dynamicTyping: true}).data;

            // For each row in data, create a marker and add it to the map
            // For each row, columns `Latitude`, `Longitude`, and `Title` are required
            let markers = L.markerClusterGroup();

            for (let i in data) {
                let row = data[i];

                // console.log(row.Latitude, row.Longitude);
                let latitude = row.Latitude;
                let longitude = row.Longitude;
                let marker = L.marker([latitude, longitude], {
                    opacity: 1
                }).bindPopup(row.Title);

                markers.addLayer(marker);
                console.log(marker);

            }

            markers.addTo(map);
            layerControl.addOverlay(markers, filename, overlayLayerName);
            M.toast({
                html: 'File Added',
                className: 'rounded green accent-3'
            });
            map.spin(false);
        });

    } //loadCSV


    function loadKML(url, filename) {
        var markers = L.markerClusterGroup();
        // Load kml file
        fetch(url)
            .then(res => res.text())
            .then(kmltext => {
                // Create new kml overlay
                const parser = new DOMParser();
                const kml = parser.parseFromString(kmltext, 'text/xml');
                const track = new L.KML(kml);
                // map.addLayer(track);
                markers.addLayer(track);
                markers.addTo(map);
                layerControl.addOverlay(markers, filename, overlayLayerName);
                // Adjust map to show the kml
                map.flyToBounds(track.getBounds());
                M.toast({
                    html: 'File Added',
                    className: 'rounded green accent-3'
                });
                map.spin(false);
            });
    } //loadKML


    function loadUploadedGeoJsonFile(data, layerName, groupName, level = 0, fitZoom = true) {

        //var itemName = data.name == undefined ? filename.split('.')[0] : data.name;
        var geojsonPointMarkerClusters = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            disableClusteringAtZoom: 16
        });

        totalAreaOfTimorLeste = 0;
        geometry_type = "";
        let geoJsonFileResult = L.geoJson(data, {
            style: function (feature) {
                console.log("feature.properties.color: " + feature.properties.color);
                if (feature.properties.color != null) {
                    return {
                        color: "#960707",
                        fillOpacity: 1,
                        stroke: "#000",
                        weight: 2,
                        dashArray: '3',
                        fillColor: feature.properties.color
                    };
                } else if (feature.properties.ph != null) {
                    return {
                        fillColor: getSoilPHColor(feature.properties.ph),
                        weight: 2,
                        opacity: 1,
                        color: 'white',
                        dashArray: '3',
                        fillOpacity: 0.35
                    };
                } else {
                    return {color: "#960707"};
                }
            },

            pointToLayer: function (feature, latlng) {
                if (feature.geometry.type == "Point") {
                    let icon = new L.icon({
                        iconSize: [45, 60], // width and height of the image in pixels
                        popupAnchor: [0, 0], // point from which the popup should open relative to the iconAnchor
                        iconUrl: iconUrl //feature.properties.icon
                    });

                    return L.marker(latlng, {icon: icon});
                }

            },
            onEachFeature: function (feature, layer) {

                let array_of_area = feature.geometry.coordinates;
                let total_area = 0;
                geometry_type = feature.geometry.type;


                if (geometry_type == "Polygon") {
                    let polygon = turf.polygon(feature.geometry.coordinates);
                    total_area = (turf.area(polygon) / 1000000).toFixed(2);
                } else if (geometry_type == "MultiPolygon") {
                    // console.log("Begin MultiPolygon");
                    for (let i = 0; i < array_of_area.length; i++) {
                        let coordinates = array_of_area[i];
                        let polygon = turf.polygon(coordinates);
                        //
                        total_area = (turf.area(polygon) / 1000000).toFixed(2);
                    }
                }

                totalAreaOfTimorLeste += parseFloat(total_area);

                var popupText = 'geometry type: ' + feature.geometry.type;

                if (feature.properties.color) {
                    popupText += '<br/>color: ' + feature.properties.color;
                }
                //
                if (feature.properties.ID_Area !== undefined) {
                    layer.bindTooltip('<p>' + feature.properties.ID_Area + '</p>', {
                        closeButton: false,
                        // offset: L.point(0, -20),
                        // direction: 'right',
                        permanent: false,
                        sticky: true,
                        // offset: [10, 0],
                        opacity: 0.75,
                        // className: 'leaflet-tooltip'
                    });
                }

                var properties = layer.feature.properties;

                var column = Object.keys(properties);

                // console.log(column);

                var html = layerName.toUpperCase() + "<hr/>" +
                    "<ul>";

                if ((geometry_type == "Polygon") || (geometry_type == "MultiPolygon")) {
                    html += "<li>Area: " + total_area + " km<sup>2</sup></li>";
                }


                for (var i = 0; i < column.length; i++) {

                    $.each(properties, function (key, value) {
                        html += "<li>" + key + " - " + value + "</li>";
                    });
                    break;
                }

                html += "</ul>";


                layer.on({
                    click: function (e) {

                        $("#feature-title").html(layerName.toUpperCase());

                        $("#feature-info").html(html);
                        $("#featureModal").modal();
                        $("#featureModal").modal('open');
                    },
                    mouseover: function (e) {
                        layer.bindPopup(html).openPopup();
                        // layer.setStyle({
                        //     weight: 4,
                        //     fillOpacity: 0.7
                        // });

                        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                            layer.bringToFront();
                        }
                    },
                    mouseout: function (e) {
                        layer.bindPopup(html).closePopup();
                        geoJsonFileResult.resetStyle(e.target);
                    },
                    remove: function (e) {
                        $("#feature-title").html("");
                        $("#feature-info").html("");
                        $("#featureModal").modal("close");
                    }
                });

            }
        });

        // geoJsonFileResult.addTo(map);

        if (geometry_type == "Point") {
            geojsonPointMarkerClusters.addLayer(geoJsonFileResult);
            geojsonPointMarkerClusters.addTo(map);
        } else {
            geoJsonFileResult.addTo(map);
        }

        geoJsonFileResult.StyledLayerControl = {
            removable: true,
            visible: false
        }


        if (fitZoom) {
            map.flyToBounds(geoJsonFileResult.getBounds());
        }
        var strGroup = "";
        if (groupName != null && groupName !== undefined) {
            strGroup = groupName;
        } else {
            strGroup = layerName;
        }

        if (geometry_type == "Point") {
            layerControl.addOverlay(geojsonPointMarkerClusters, layerName, strGroup);
        } else {
            layerControl.addOverlay(geoJsonFileResult, layerName, strGroup);
        }


        if (geometry_type == "Polygon" && geometry_type == "MultiPolygon") {
            M.toast({
                html: "Total area: " + totalAreaOfTimorLeste.toFixed(2) + ' km<sup>2</sup>',
                className: 'rounded green accent-3'
            })
        }


        $("#container-external-layer").removeClass('display-container-external-layer');
        $("#container-external-layer").addClass('hide');


    }// loadUploadedGeoJsonFile


    $("body").on("keyup", "#sidenav-input-search", function () {
        var value = $(this).val().toLowerCase();
        $("#sidenav-list-poi li").show().filter(function () {
            return ($(this).text().toLowerCase().trim().indexOf(value) == -1);
        }).hide();
    });

})
