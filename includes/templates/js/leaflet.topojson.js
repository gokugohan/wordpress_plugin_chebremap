L.TopoJSON = L.GeoJSON.extend(
{
    addData: function(jsonData) 
    {    
        var geojson, key;
        if (jsonData.type === "Topology") 
        {
            for (key in jsonData.objects) 
            {
                if (jsonData.objects.hasOwnProperty(key)) {
                    geojson = topojson.feature(jsonData, jsonData.objects[key]);
                    L.GeoJSON.prototype.addData.call(this, geojson);
                }
            }
            return this;
        }    
        else 
        {
            L.GeoJSON.prototype.addData.call(this, jsonData);
        }

        return this;
    }  
});



L.topoJson = function (data, options) {
    return new L.TopoJSON(data, options);
};

async function addTopoData(url) {
    let response = await fetch(url);
    let data = await response.json();
    return data;
}
