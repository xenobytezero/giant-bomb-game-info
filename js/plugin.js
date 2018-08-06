const { registerPlugin } = wp.plugins;
const { PanelBody, TextControl } = wp.components;
const { PluginSidebar } = wp.editPost;
const { Component, Fragment } = wp.element;
const { withSelect, withDispatch } = wp.data;
const { compose } = wp.compose;
const apiRequest = wp.apiRequest;

import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faGamepad, faCog, faSearch } from '@fortawesome/free-solid-svg-icons'

import { GBQuery } from 'js/gb_query.js';
import { META_KEY, wrapApiRequest } from './core';

class GBGISidebar extends Component {

    constructor(props){

        super(props);

        this.props = props;

        this.state = {
            hasApiKey: false,
            attemptedAPIKey: false,
            apiKey: null,
            tempApiKey: null,

            currentGame: null,

            searchTerm: '',
            searchResults: null,
            isSearching: false,
            hasSearched: false,
            
            authPanelOpen: true
        }


    }

    // ---------------------------------------------------

    _getAPIKey() {
        return wrapApiRequest({
            path: '/gbgi/v1/apiKey',
            method: 'GET'
        })
    }

    _setAPIKey(apiKey) {
        return wrapApiRequest({
            path: '/gbgi/v1/apiKey',
            method: 'POST',
            data: { apiKey: apiKey }
        })
    }

    _revokeAPIKey() {
        return wrapApiRequest({
            path: '/gbgi/v1/apiKey',
            method: 'DELETE'
        })
    }

    // ---------------------------------------------------

    _updateGBGIMeta(gameData) {
        let metaObj = {};
        let actualGameData = null;

        if (gameData !== null){
            actualGameData = JSON.stringify(gameData)
        }
        
        metaObj[META_KEY] = actualGameData;
        this.props.updateMeta(metaObj);
    }

    _updateAPIKey(apiKey){
        this.setState({
            apiKey: apiKey,
            hasApiKey: true,
            authPanelOpen: false
        })
    }

    // ---------------------------------------------------

    _convertPlatformsToString(platformArray) {
        return platformArray.join(', ');
    }

    // ---------------------------------------------------

    _searchForGame(searchTerm) {

        this.setState({
            isSearching: true,
            searchResults: null
        });

        GBQuery.searchForGame(this.state.apiKey, searchTerm)
            .then((apiResult) => {

                let results = apiResult.results.map((obj) => {
                    let dataObj = GBQuery.apiResultToDataObj(obj);
                    return {
                        source: dataObj,
                        display: dataObj.title + ' (' + dataObj.platforms.join(', ') + ')'  
                    };      
                });

                this.setState({
                    searchResults: results,
                    isSearching: false
                });

            })

    }

    _clearCurrentGame() {
        this._updateGBGIMeta(null);
        this.setState({
            currentGame: null
        });
    }

    // ---------------------------------------------------
    // ---------------------------------------------------

    _onSearchResultClicked(resultObj) {

        this._updateGBGIMeta(resultObj);

        this.setState({
            searchResults: null,
            searchTerm: '',
            currentGame: resultObj
        });

    }

    _onSetApiKeyClicked(apiKey) {

        this._setAPIKey(apiKey)
            .then(() => { this._updateAPIKey(apiKey); })

    }

    _onSearchBoxKeyPress(e) {
        if (e.key === 'Enter') { 
            this._searchForGame(this.state.searchTerm); 
        }
    }

    // ---------------------------------------------------
    // ---------------------------------------------------

    componentDidMount() {

        this._getAPIKey()
            .then((apiKey) => { 
                return this._updateAPIKey(apiKey);
            })
            .finally(() => {

                this.setState({
                    attemptedAPIKey: true
                });
                
                let meta = this.props.meta[META_KEY];
                if (meta !== ""){
                    let json = JSON.parse(this.props.meta[META_KEY]);
                    this.setState({
                        currentGame: json
                    })
                }

            })

    }

    render() {


        return <PluginSidebar name="gbgi-sidebar"><div class="gbgi-sidebar">

            {!this.state.attemptedAPIKey && <h3 class="pholder">Loading...</h3>}
    
            {this.state.attemptedAPIKey && <Fragment>
                
                <PanelBody
                    title={"API Key - " + (this.state.hasApiKey ? 'OK' : 'Missing' )}
                    opened={this.state.authPanelOpen}
                    onToggle={() => { this.setState({authPanelOpen: !this.state.authPanelOpen})}}
                >

                    {this.state.apiKey === null && <Fragment>

                        <p>This plugin requires a Giant Bomb account and an API Key to function to work</p>
                        <p>If you already have an account, get your API key <a href="https://www.giantbomb.com/api/">from here</a></p>
                    
                        <TextControl
                            value={this.tempApiKey}
                            onChange={(val) => {this.setState({tempApiKey: val}) }}
                        />

                        <button
                            class="button button-primary fullwidth"
                            onClick={() => { this._onSetApiKeyClicked(this.state.tempApiKey) ; }}
                        >Set API Key</button>

                    </Fragment>}

                    {this.state.apiKey !== null && <Fragment>
                        <p>API Key set, your good to go!</p>,
                        <button
                            class="button button-primary fullwidth"
                            onClick={() => { this._revokeAPIKey() ; }}
                        >Revoke API Key</button>
                    </Fragment>}

                </PanelBody>


                <PanelBody
                    title={'Current Game' + (this.state.currentGame === null ? '' : ' - ' + this.state.currentGame.title)}
                >
                    
                    {this.state.currentGame === null && 
                        <h3 class="pholder">No Game Selected</h3>
                    }

                    {this.state.currentGame !== null && 
                        <div class="current-game">
                            <img class="image" src={this.state.currentGame.imageUrl}/>
                            <p class="platforms">{this._convertPlatformsToString(this.state.currentGame.platforms)}</p>
                            <button
                                class="button button-primary fullwidth"
                                onClick={() => { this._clearCurrentGame(); }}
                            >Clear Game</button>
                        </div>
                    }

                </PanelBody>

                <PanelBody
                    title={'Game Search'}
                >

                    <div class="search-area">
                        
                        <div class="controls">

                            <input type="text"
                                class='search-input'
                                value={this.state.searchTerm}
                                onChange={(e) => { this.setState({searchTerm: e.target.value}); }}
                                onKeyPress={(e) => { this._onSearchBoxKeyPress(e); }}
                            />

                            <button 
                                class="button button-primary" 
                                onClick={(e) => { this._searchForGame(this.state.searchTerm); } }
                                onKeyPress={this._onSearchBoxKeyPress}
                            ><FontAwesomeIcon icon={faSearch}/></button>

                        </div>

                        {this.state.isSearching && <FontAwesomeIcon className={'loading-spinner'} icon={faCog} size="2x" spin/>}

                        {this.state.searchResults !== null && this.state.searchResults.length === 0 && <h3 class="pholder">No Results</h3>}

                        {this.state.searchResults !== null && this.state.searchResults.length !== 0 && <Fragment>
                            
                            <hr/>

                            <ul class="results">
                                {this.state.searchResults.map((result, index) => (
                                    <li><a onClick={() => this._onSearchResultClicked(result.source)}>
                                        {result.display}
                                    </a></li>
                                ))}
                            </ul>

                        </Fragment>}

                    </div>

                </PanelBody>

            </Fragment>}

        </div></PluginSidebar>


    }

}

// --------------------------------------------------------------
// --------------------------------------------------------------
// --------------------------------------------------------------

// Fetch the post meta.
const applyWithSelect = withSelect( ( select ) => {
	const { getEditedPostAttribute } = select( 'core/editor' );

	return {
        meta: getEditedPostAttribute( 'meta' )
    };
    
} );

// Provide method to update post meta.
const applyWithDispatch = withDispatch( ( dispatch, { meta } ) => {
	const { editPost } = dispatch( 'core/editor' );

	return {
		updateMeta( newMeta ) {
			editPost( { meta: Object.assign(meta, newMeta) } ); // Important: Old and new meta need to be merged in a non-mutating way!
        }
    };
    
} );

// --------------------------------------------------------------
// --------------------------------------------------------------
// --------------------------------------------------------------

registerPlugin('gbgi', {
    icon: <FontAwesomeIcon icon={faGamepad}/>,
    render: compose([
        applyWithSelect,
        applyWithDispatch
    ])( GBGISidebar )
});