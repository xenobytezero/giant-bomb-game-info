import fetchJSONP from 'fetch-jsonp/build/fetch-jsonp';
import qs from 'qs/lib/index';

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