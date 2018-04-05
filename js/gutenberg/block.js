const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { withAPIData } = wp.components;
const { Component } = wp.element;

import * as GBGICore from '../gbgi_core.js';

//import './style.scss';
import './style-editor.scss';

class Edit extends Component {

    constructor(props) {
        super(...arguments);

        this.props = props;

        this.state = {
            searchResults: [],
            searchTerm: ""
        }
    }

    onSearchTermChanged(e) {
        this.setState({searchTerm: e.target.value});
    }

    onSearchClick(e) {

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
                    searchResults: results
                });
            
            });

    }

    onSearchResultClicked(resultObj) {
        this.props.setAttributes({ 
            gameInfo: JSON.stringify(resultObj)
        });

        this.setState({
            searchResults: []
        });
    }

    render() {

        const {attributes, className, setAttributes, apiKey} = this.props

        let gameInfoObj = {title: ""}
        if (attributes.gameInfo !== '') {
            gameInfoObj = JSON.parse(attributes.gameInfo);
        } 

        return <div className={className}>

            <div class="display-area">
                <img src={gameInfoObj.imageUrl}/>
                <h3>{gameInfoObj.title}</h3>
                <p>PLATFORMS</p>
            </div>

            <hr/>

            <div class="search-area">
                <input type="text" 
                    value={this.state.searchTerm} 
                    onChange={(e) => { this.onSearchTermChanged(e) } } />

                <button class="button button-primary" onClick={(e) => { this.onSearchClick(e); } }>Search</button>
            </div>
            <hr/>

            <ul class="results">
            {this.state.searchResults.map((result, index) => (
                <li onClick={() => this.onSearchResultClicked(result.source)}>{result.display}</li>
            ))}
            </ul>

        </div>;


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
        }
    },

    edit: withAPIData(() => {
        return {
            apiKey: '/gbgi/v1/apiKey'
        }
    })(Edit),

    save() {
        return <p>Page View</p>;
    }


})
