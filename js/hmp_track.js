var _avp = _avp || [];

Drupal.behaviors.hmp_track = {
	attach: function (context, settings) {
	  console.log(settings.hmp_track);
	  
      loadAdvertAVP(settings.hmp_track['advertserve_url']);
	  loadDMD(settings.hmp_track['dmd_id']);
	}
};

function loadDMD(dmd_id) {
	(function(w,d,s,m,n,t){
        w[m]=w[m]||{init:function(){(w[m].q=w[m].q||[]).push(arguments);},ready:function(c){if('function'!=typeof c){return;}n.onload=n.onreadystatechange=function(){
        if(!n.readyState||/loaded|complete/.test(n.readyState)){n.onload=n.onreadystatechange=null;if(t.parentNode&&n.parentNode){t.parentNode.removeChild(n);}if(c){c();}}};}},
        w[m].d=1*new Date();n=d.createElement(s);t=d.getElementsByTagName(s)[0];n.async=1;n.src='//www.medtargetsystem.com/javascript/beacon.js?v2.5.11';
        t.parentNode.insertBefore(n,t);
    })(window,document,'script','AIM');

    AIM.init(dmd_id);
    console.log(dmd_id);
}

function loadAdvertAVP(server) {
    (function() {
    function load() {
      var s = document.createElement('script');
      s.type = 'text/javascript'; s.async = true; s.src = server;
      var x = document.getElementsByTagName('script')[0];
      x.parentNode.insertBefore(s, x);
    }
    window.setTimeout(load, 250);
    })();
}