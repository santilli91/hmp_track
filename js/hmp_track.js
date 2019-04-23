var _avp = _avp || [];
var _hmpHasRun = _hmpHasRun || 0;
Drupal.behaviors.hmp_track = {
  attach: function (context, settings) {
    if(_hmpHasRun == 0) {
      loadAdvertAVP(settings.hmp_track.config['advertserve_url'],settings.hmp_track.config['advertserve_timeout']);
      loadDMD(settings.hmp_track.config['dmd_id']);
      proclivityPX(settings.hmp_track);
      woopraScripts(settings.hmp_track);
      _hmpHasRun = 1;
    }
  }
};

function loadDMD(dmd_id) {
  if(dmd_id != '') {
    (function(w,d,s,m,n,t){
      w[m]=w[m]||{init:function(){(w[m].q=w[m].q||[]).push(arguments);},ready:function(c){if('function'!=typeof c){return;}n.onload=n.onreadystatechange=function(){
      if(!n.readyState||/loaded|complete/.test(n.readyState)){n.onload=n.onreadystatechange=null;if(t.parentNode&&n.parentNode){t.parentNode.removeChild(n);}if(c){c();}}};}},
      w[m].d=1*new Date();n=d.createElement(s);t=d.getElementsByTagName(s)[0];n.async=1;n.src='//www.medtargetsystem.com/javascript/beacon.js?v2.5.11';
      t.parentNode.insertBefore(n,t);
    })(window,document,'script','AIM');

    AIM.init(dmd_id);
    AIM.ready(function(){
      AIM.ondetect(cb_ondetect);
    });
  }
}

//AIM Signal Handler
function cb_ondetect(json){
  var _npi = json.npi_number; 
  if(_npi) {
    npi = _npi;
    document.cookie = "tar_enc_npi=" + npi;
  }
  console.log('cb_ondetect running');  
}

function loadAdvertAVP(server,timeout) {
  if(server != '') {
    (function() {
    function load() {
      var s = document.createElement('script');
      s.type = 'text/javascript'; s.async = true; s.src = server;
      var x = document.getElementsByTagName('script')[0];
      x.parentNode.insertBefore(s, x);
    }
    if(timeout != '')
      window.setTimeout(load, timeout);
    })();
  }
}

function proclivityPX(config) {
  if(config.config['proclivity_px']) {
    wec.browse( { 
      token :  config.config['proclivity_px_token'],
      user_id : config.npi,
      pubid:  '635',
      siteid :  '63502',
      puid: config.uid
    } );
  }
}

function woopraScripts(config) {
  if(config.config['woopra_id'] != '') {
    (function(){
          var t,i,e,n=window,o=document,a=arguments,s="script",r=["config","track","identify","visit","push","call","trackForm","trackClick"],c=function(){var t,i=this;for(i._e=[],t=0;r.length>t;t++)(function(t){i[t]=function(){return i._e.push([t].concat(Array.prototype.slice.call(arguments,0))),i}})(r[t])};for(n._w=n._w||{},t=0;a.length>t;t++)n._w[a[t]]=n[a[t]]=n[a[t]]||new c;i=o.createElement(s),i.async=1,i.src="//static.woopra.com/js/w.js",e=o.getElementsByTagName(s)[0],e.parentNode.insertBefore(i,e)
    })("woopra");
    woopra.config({
        domain: config.config['woopra_id'],
        ping_interval:5000
    });
    if(config.email) {
      woopra.identify({
          id: config.email['hash'],
          email: config.email['plain'],
      });
    }
    if(config.npi != '') {
      woopra.identify({
        npi: config.npi,
      })
    }
    if(config.auth0) {
      woopra.identify({
        first_name: config.auth0.first_name,
        last_name: config.auth0.last_name,
        degree: config.auth0.degree,
        specialty: config.auth0.specialty
      })
    }

    woopra.track("pv",{
        url: window.location.pathname,
        title: document.title,
        topics: config.topics,
    });
  }
}
