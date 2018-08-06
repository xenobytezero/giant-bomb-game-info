const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { PanelBody, SelectControl } = wp.components;
const { Component, Fragment } = wp.element;
const { compose } = wp.compose;
const { withSelect } = wp.data;

import { wrapApiRequest, META_KEY } from '../../js/core';

class Edit extends Component {

    constructor(props) {

        super(...arguments);

        this.props = props;

        this.state = {
            hasTemplateList: false,
            templateList: null,

            gameInfoObj: null
        }

    }

    convertPlatformsToString(platformArray) {
        return platformArray.join(', ');
    }

    // -----------------------------------------

    _getTemplateList() {
        return wrapApiRequest({
            path: '/gbgi/v1/templates',
            method: 'GET'
        })
    }

    componentDidMount() {

        this._getTemplateList()
            .then((templateList) => {
                this.setState({
                    hasTemplateList: true,
                    templateList: templateList
                });
            })

    }

    render() {

        const {
            attributes, 
            setAttributes, 
            className
        } = this.props;

        return <Fragment>

            <InspectorControls>

                <PanelBody title={'Custom Template'}>

                    {(!this.state.hasTemplateList && <p>Loading Templates...</p>)}

                    {(this.state.hasTemplateList &&
                        <SelectControl
                            label={'Custom Template'}
                            value={attributes.customTemplate}
                            options={this.state.templateList.map((t) => {
                                return {label: t.name, value:t.value}
                            })}
                            onChange={(val) => setAttributes({customTemplate: val})}
                        />
                    )}

                </PanelBody>
                
            </InspectorControls>
            

            <div class="editor" className={className + ' editor'}>

                {this.props.gameInfoAsObject === null && <div class="components-placeholder">
                    <h3 class="components-placeholder__label">No Game Selected.</h3>
                </div>
                }

                {this.props.gameInfoAsObject !== null && <div class="default-block">
                    <img class="image" src={this.props.gameInfoAsObject.imageUrl}/>
                    <h3 class="title">{this.props.gameInfoAsObject.title}</h3>
                    <p class="platforms">{this.convertPlatformsToString(this.props.gameInfoAsObject.platforms)}</p>
                </div>}

            </div>

        </Fragment>

    }

}
// --------------------------------------------------------------
// --------------------------------------------------------------
// --------------------------------------------------------------

const applyWithSelect = withSelect((select) => {

    let parseGameInfoMeta = () => {

        let gameInfoObj = null;
        
        let meta = select('core/editor').getEditedPostAttribute('meta');
        let gameInfoJson = meta[META_KEY];

        if (gameInfoJson !== ""){
            gameInfoObj = JSON.parse(gameInfoJson);
        }

        return gameInfoObj;

    };

    return {
        gameInfoAsObject: parseGameInfoMeta()
    }

});


// --------------------------------------------------------------
// --------------------------------------------------------------
// --------------------------------------------------------------

registerBlockType('gbgi/gbgi-block', {
    title: 'GBGI Game Info',
    category: 'widgets',

    attributes: {
        gameInfo: {
            type: 'string',
            source: 'meta',
            meta: META_KEY
        },
        customTemplate: {
            type: 'string',
            default: ''
        }
    },

    edit: compose([applyWithSelect])(Edit),

    save() {
        return null;
    }

})
