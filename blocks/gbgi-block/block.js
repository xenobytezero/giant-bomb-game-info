const { registerBlockType, InspectorControls, BlockControls } = wp.blocks; // Import registerBlockType() from wp.blocks
const { withAPIData, PanelBody, CheckboxControl, SelectControl } = wp.components;
const { Component } = wp.element;

import * as GBGICore from '../../js/gbgi_core.js';

class Edit extends Component {

    constructor(props) {
        super(...arguments);

        this.props = props;

        this.state = {
            searchResults: [],
            searchTerm: "",
            isSearching: false
        }

        this.toolbarSettings = [{
            icon: 'trash',
            title: 'Remove',
            onClick: () => { this.onRemoveClicked() }
        }]

    }

    onSearchTermChanged(e) {
        this.setState({searchTerm: e.target.value});
    }

    onSearchClick(e) {

        this.setState({
            isSearching: true
        });

        GBGICore.searchForGame(this.props.apiKey.data, this.state.searchTerm)
            .then((apiResult) =>{

                let results = apiResult.results.map((obj) => {
                    let dataObj = GBGICore.apiResultToDataObj(obj);
                    return {
                        source: dataObj,
                        display: dataObj.title + ' (' + dataObj.platforms.join(', ') + ')'  
                    };      
                });

                this.setState({
                    searchResults: results,
                    isSearching: false
                });
            
            });

    }

    onSearchResultClicked(resultObj) {

        let json = JSON.stringify(resultObj)

        this.props.setAttributes({ 
            gameInfo: json,
            gameInfoJson: json
        });

        this.setState({
            searchResults: [],
            searchTerm: ''
        });
    }

    onRemoveClicked() {
        this.props.setAttributes({ 
            gameInfo: '',
            gameInfoJson: ''
        });
    }

    convertPlatformsToString(platformArray) {
        return platformArray.join(', ');
    }

    render() {

        const {attributes, className, setAttributes, apiKey, customTemplate, isSelected} = this.props;

        let gameInfoObj = null;

        if (attributes.gameInfo !== '') {
            gameInfoObj = JSON.parse(attributes.gameInfo);
        } 

        return [ 

            isSelected && <BlockControls controls={this.toolbarSettings}/>,

            isSelected && (<InspectorControls>
                <PanelBody title={'Game Search'} className={className}>

                    {(apiKey.isLoading && <p>Loading...</p>)}

                    {(!apiKey.isLoading && 
                        <div class="search-area">
                            
                            <input type="text" 
                                value={this.state.searchTerm} 
                                onChange={(e) => { this.onSearchTermChanged(e) } } />

                            <button 
                                class="button button-primary" 
                                onClick={(e) => { this.onSearchClick(e); } }>Search</button>

                        </div>
                    )}
                    
                    {/* Only show the hr if there are search results */}
                    {(this.state.searchResults.length > 0 || this.state.isSearching) && <hr/>}

                    {(this.state.isSearching && <p class="searching">Searching...</p>)}

                    <ul class="results">
                        {this.state.searchResults.map((result, index) => (
                            <li><a onClick={() => this.onSearchResultClicked(result.source)}>
                                {result.display}
                            </a></li>
                        ))}
                    </ul>

                </PanelBody>

                <PanelBody title={'Rendering'}>
                    <CheckboxControl 
                        label={'Disable Block Rendering'}
                        checked={attributes.disableRender} 
                        onChange={(val) => setAttributes({disableRender: val})} 
                    />
                    {(customTemplate.isLoading && <p>Loading...</p>)}
                    {(!customTemplate.isLoading &&
                    <SelectControl
                        label={'Custom Template'}
                        value={attributes.customTemplate}
                        options={customTemplate.data.map((t) => {
                            return {label: t.name, value:t.path}
                        })}
                        onChange={(val) => setAttributes({customTemplate: val})}
                    />
                    )}

                </PanelBody>


            </InspectorControls>),

            <div class="editor" className={className + ' editor'}>

                {gameInfoObj == null && <div class="components-placeholder">
                    <h3 class="components-placeholder__label">No Game Selected.</h3>
                </div>
                }

                {gameInfoObj != null && <div class="default-block">
                    <img class="image" src={gameInfoObj.imageUrl}/>
                    <h3 class="title">{gameInfoObj.title}</h3>
                    <p class="platforms">{this.convertPlatformsToString(gameInfoObj.platforms)}</p>
                </div>}

            </div>
        ]


    }



}

registerBlockType('gbgi/gbgi-block', {
    title: 'GBGI Game Info',
    category: 'widgets',

    attributes: {
        gameInfo: {
            type: 'string',
            source: 'meta',
            meta: 'gbgi-gameinfo'
        },
        gameInfoJson: {
            type: 'string',
            default: ''
        },
        disableRender: {
            type: 'boolean',
            default: false
        },
        customTemplate: {
            type: 'string',
            default: ''
        }
    },

    edit: withAPIData(() => {
        return {
            apiKey: '/gbgi/v1/apiKey',
            customTemplate: '/gbgi/v1/templates'
        }
    })(Edit),

    save() {
        return null;
    }

})
