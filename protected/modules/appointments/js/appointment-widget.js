/**
 * Handle widget request
 */
if(udaHost != ''){
	var udaHost = (typeof udaHost === 'undefined') ? '' : udaHost;
	var udaKey = (typeof udaKey === 'undefined') ? '' : udaKey;
	var udaWidth = (typeof udaWidth === 'undefined') ? '100%' : udaWidth;
	var udaHeight = (typeof udaHeight === 'undefined') ? '100%' : udaHeight;
	var udaLang = (typeof udaLang === 'undefined') ? '' : udaLang;
	
	if(udaKey != '' && udaHost != ''){
		
		var encoded_host = encode64(udaHost);
		var encoded_key = encode64(udaKey);
		var encoded_lang = udaLang != '' ? encode64(udaLang) : '';
		
		var filePath = 'integrationWidgets/integrationAppointment/?show=widget&host='+encoded_host+'&key='+encoded_key+'&lang='+encoded_lang;
		
		// Setup the iframe target
		var iframe = '<iframe id="frame" name="widget-center-panel" src="#" width="'+udaWidth+'" height="'+udaHeight+'" marginheight="0" marginwidth="0" frameborder="no" scrolling="yes"></iframe>';
		// Write the iframe to the page
		document.write(iframe);
		 
		var myIframe = parent.document.getElementById('frame');
		// Setup the width and height
		//myIframe.width = 800;
		//myIframe.height = 180;
		 
		myIframe.src = udaHost+filePath;

		// Set the style of the iframe
		myIframe.style.border = '1px solid #aaa';
		//myIframe.style.padding = '0px';
	}	
}

/**
 * Returns base64 encodded string
 * @param string
 * @return string 
 */
function encode64(input){
	var keyStr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';    

	input = escape(input);
    var output = '';
    var chr1, chr2, chr3 = '';
    var enc1, enc2, enc3, enc4 = '';
    var i = 0;

    do{
        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if(isNaN(chr2)){
           enc3 = enc4 = 64;
        }else if(isNaN(chr3)){
           enc4 = 64;
        }

        output = output + keyStr.charAt(enc1) + keyStr.charAt(enc2) + keyStr.charAt(enc3) + keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = '';
        enc1 = enc2 = enc3 = enc4 = '';
    } while (i < input.length);

    return output;
}
