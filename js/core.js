const apiRequest = wp.apiRequest;

function wrapApiRequest(requestObj) {
    return new Promise((res, rej) => {
        apiRequest(requestObj) 
        .then((data, textStatus, jqXHR) => { return res(data, textStatus, jqXHR)})
        .fail((jqXHR, textStatus, errorThrown) => { return rej(jqXHR, textStatus, errorThrown); })
    });
}

let META_KEY = 'gbgi_gameinfo';

export {
    wrapApiRequest,
    META_KEY
}