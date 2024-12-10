class Request{
    #request;
    #output;
    /**
     * Send a reques to get information
     * @param {String} url URL to send
     * @returns {Request}
     */
    constructor(url){
        this.#request = this.#sanitizeUrl(url);
        this.#output = {};
        return this;
    }
    /**
     * Sanatizes the URL
     * @param {String} url Url to sanitize
     * @returns Sanitized URL
     */
    #sanitizeUrl(url) {
        url = url.replace(/\s+|<|>/g, ''); // 1. Remove whitespace and special characters
        url = decodeURIComponent(url); // 2. Decode URL-encoded characters
        url = url.replace(/^https?:\/\/|#.*/g, ''); // 3. Strip protocol and fragment
        url = url.trim(); // 4. Trim
        return url;
    }
    /**
     * Send Request
     * @returns {Request}
     */
    send(){
        const xhr = new XMLHttpRequest();
        xhr.onload = ()=>{
            if(xhr.readyState==4&&xhr.status==200){
                this.#output = {
                    status: xhr.status,
                    responce: xhr.response,
                    txt: xhr.responseText
                }
            }else{
                this.#output = {
                    status:xhr.status, 
                    responce: xhr.response,
                    txt: xhr.responseText
                }
            }
        }
        xhr.open('GET',this.#request,false);
        xhr.send();
        return this;
    }
    /**
     * Triggers a callback on success
     * @param {Function} callback Success callback
     * @returns {Request}
     */
    onSuccess(callback){
        if(this.#output.status==200)
            callback(this.#output.responce, this.#output.status);
        return this;
    }
    /**
     * Triggers a callback on fail
     * @param {Function} callback Failed callback
     * @returns {Request}
     */
    onError(callback){
        if(this.#output.status!=200)
            callback(this.#output.responce, this.#output.status);
        return this;
    }
}