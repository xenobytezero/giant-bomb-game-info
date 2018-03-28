jQuery(function() {

    let root = jQuery('#gbgi-metabox');
    let gbid = root.find('.gb-game-id');
    let apiKeyElement = root.find('.api-key');

    let searchButton = root.find('.do-search');
    let searchQuery = root.find('.search-query');
    let searchResults = root.find('.results');
    
    let loading = root.find('.loading');
    let results = root.find('.results');
    
    searchResults.on('click', 'li', (e) => {
        let id =
            GBInfo.getGameResourceId() +
            '-' +
            jQuery(e.target).data('gbid');
            
        gbid.val(id);
        searchResults.empty();
    });
    
    
    searchButton.on('click', () => {
        
        results.fadeOut(400, () => { loading.fadeIn(400); });
        
        let query = searchQuery.val();
        let apiKey = apiKeyElement.val();

        GBInfo.searchForGame(apiKey, query)
            .then((data) => {
            
                searchResults.empty();
                
                loading.fadeOut(400, () => { results.fadeIn(400); });
                
                data.results.forEach((obj) => {
                    
                    var dataObj = GBInfo.apiResultToDataObj(obj);

                    let res = dataObj.title + '(' + dataObj.platforms.join(', ') + ')';
                    
                    let item = jQuery('<li>')
                        .text(res)
                        .data('gbgi_data', dataObj);

                    searchResults.append(item);
                    
                });
                
            });
    
    });
    
});
