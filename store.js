/*

Simple script to manage data on the frontend via local storage. Cookies are used as a fallback
A simple extension to this script will allow it to support JSON parsing and stringifying


Dependencies:
jQuery 1.5++
jquery.cookie plugin


Usage:
// stores a chunk of data called friend with the value Bob for the life of the session
storage.set('friend', 'Bob');

// to store persistant data
storage.set('enemy', 'Phil', true);

// get our data
storage.get('Bob');

// clear our data
storage.remove('Bob');

*/


(function($) {

	// is local storage supported?
	var supportsLocalStorage = (function() {
	  try {
	    return !!localStorage.getItem;
	  } catch(e) {
	    return false;
	  }
	}()),
	// is session storage supported?
	supportsSessionStorage = (function() {
	    try {
	      return 'sessionStorage' in window && window['sessionStorage'] !== null;
	    } catch(e){
	      return false;
	    }
	}());

	var storage = {

	    'set' : function(key,value,keep){
	        if (!key && !value) return false;
	        if (keep) {
	            if (supportsLocalStorage) {
	                window.localStorage.setItem(key, value);
	            } else {
	                $.cookie(key, value, {
	                   expires: 365,
	                   path: '/'
	                });
	            }
	        } else {
	            if (supportsSessionStorage) {
	                window.sessionStorage.setItem(key, value);
	            } else {
	                $.cookie(key, value, {path: '/'});
	            }
	        }
	    },
	    'get' : function(key){
	        if (!key) return false;
	        if (supportsSessionStorage && window.sessionStorage.getItem(key)) {
	            return window.sessionStorage.getItem(key);
	        } else if (supportsLocalStorage && window.localStorage.getItem(key)) {
	            return window.localStorage.getItem(key);
	        } else if ($.cookie(key)) {
	            return $.cookie(key);
	        } 
	        return false;
	    },       
	    'remove' : function(key){
	        if (!key) return false;
	        if (supportsSessionStorage && window.sessionStorage.getItem(key)) {
	             window.sessionStorage.removeItem(key);
	             return true;
	        } else if (supportsLocalStorage && window.localStorage.getItem(key)) {
	           window.localStorage.removeItem(key);
	            return true;
	        } else if ($.cookie(key)) {
	            $.cookie(key, null, {
	                path: '/'
	            });
	            return true;
	        }
	        return false;
	    }
	};

}(jQuery));
