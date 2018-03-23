(function() {

    var _url = 'http://www.giantbomb.com/api/'
    var _gameTypeId = '3030';

    window.GBInfo = {
        
        getGameData: getGameData,
        searchForGame: searchForGame,
        getGameResourceId: function() {
            return _gameTypeId;
        }

    }

    function getGameData(apiKey, gameId, callBack){
        
        jQuery.ajax({
            url: _url + 'game/' + gameId,
            data: {
                api_key: apiKey,
                format: 'jsonp',
                field_list: 'name,image,platforms,site_detail_url'
            },
            dataType: 'jsonp',
            success: callBack,
            jsonp: 'json_callback' 
        });
            
    }
    
    function searchForGame(apiKey, gameQuery, callBack){
        
        jQuery.ajax({
            url: _url + 'search/',
            data: {
                api_key: apiKey,
                format: 'jsonp',
                field_list: 'name,image,platforms,id',
                query: '"' + gameQuery + '"',
                resources: 'game'
            },
            dataType: 'jsonp',
            success: callBack,
            jsonp: 'json_callback' 
        });
        
    }

})();