import * as fetchJSONP from '../node_modules/fetch-jsonp/build/fetch-jsonp';
import qs from '../node_modules/qs/dist/qs'

class OptionsHelper {

    testApi(apiKey) {

        let queryString = qs.stringify({
            api_key: apiKey,
            format: 'jsonp',
            field_list: 'name',
            query: '"Pong"',
            resources: 'game'
        });

        let url = 'https://www.giantbomb.com/api/search?' + queryString;

        return fetchJSONP(url, {
            jsonpCallback: 'json_callback'
        })

    }

}

window.OptionsHelper = new OptionsHelper();