
let _url = 'https://www.giantbomb.com/api/'
let _gameTypeId = '3030';

function searchForGame(apiKey, gameQuery){
    
    return jQuery.ajax({
        url: _url + 'search/',
        data: {
            api_key: apiKey,
            format: 'jsonp',
            field_list: 'name,image,platforms,site_detail_url',
            query: '"' + gameQuery + '"',
            resources: 'game'
        },
        dataType: 'jsonp',
        jsonp: 'json_callback',
        crossOrigin: true
    });
    
}

function apiResultToDataObj(apiResult) {

    let platforms = apiResult.platforms.map(p => p.name);

    return {
        title: apiResult.name,
        imageUrl: apiResult.image.small_url,
        platforms: platforms
    }

}

export {
    searchForGame,
    apiResultToDataObj
}