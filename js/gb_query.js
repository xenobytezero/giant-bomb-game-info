import { wrapApiRequest } from './core';

let _url = 'https://www.giantbomb.com/api/'
let _gameTypeId = '3030';

class GBQuery {

    static searchForGame(apiKey, gameQuery){
    
        return wrapApiRequest({
            url: _url + 'search/',
            data: {
                api_key: apiKey,
                format: 'jsonp',
                field_list: 'name,image,platforms,site_detail_url,deck',
                query: '"' + gameQuery + '"',
                resources: 'game'
            },
            dataType: 'jsonp',
            jsonp: 'json_callback',
            crossOrigin: true
        });
    
    }
    
    static apiResultToDataObj(apiResult) {
    
        let platforms = apiResult.platforms.map(p => p.name);
    
        return {
            title: apiResult.name,
            imageUrl: apiResult.image.small_url,
            platforms: platforms,
            description: apiResult.deck,
            link: apiResult.site_detail_url
        }
    
    }

}

export{
    GBQuery
}
