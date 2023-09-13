// alert('dotpay.js');

DOT={
    path: false,
    mainrand: '?rand='+Math.random(),
    mainjs: false,

    'h': function(s){
        return (''+s).replace(/\&/g,'&'+'amp;').replace(/\</g,'&'+'lt;').replace(/\>/g,'&'+'gt;').replace(/\'/g,'&'+'#039;').replace(/\"/g,'&'+'#034;'); // '
    },

    'alert': function(s){
	if(s=='clear') document.getElementById('dotpay_console').innerHTML='';
	else document.getElementById('dotpay_console').innerHTML+=s+'<br>';
    },

    init: async function(x){
	this.path=x;
	this.mainjs=x+'/js/';
	// this.alert("<img src='"+this.path+"/image/dotpay.webp' style='margin: auto; display: table;margin-bottom: 20px;' />");

	// get JS
        await LLOADS.LOADS_promice([
    	 DOT.mainjs+'bundle-polkadot-util.js',
         DOT.mainjs+'bundle-polkadot-util-crypto.js',
            // DOT.mainjs+'bundle-polkadot-types.js',
         DOT.mainjs+'bundle-polkadot-extension-dapp.js',
            // DOT.mainjs+'bundle-polkadot-api.js',
            // DOT.mainjs+'bundle-polkadot-keyring.js'
        ],1);

	// connect Wallets
        var wallets=await polkadotExtensionDapp.web3Enable('dotpay');
	var r={'manual':["<option value='QR'>QR-code</option>"]};
        if( !wallets.length ) {
	    DOT.alert("<b>Wallets not found</b>"
		    +"<br>You can use Wallet extention "
		    +(this.navigator()=='firefox'
			? "<a href='https://addons.mozilla.org/en-US/firefox/addon/polkadot-js-extension/'>polkadot{.js} for Firefox</a>"
			: (this.navigator()=='chrome'
			    ? "<a href='https://chrome.google.com/webstore/detail/polkadot%7Bjs%7D-extension/mopnmbcafieddcagagdcbnhejhlodfdd'>polkadot{.js} for Chrome</a>"
			    : "<a href='https://github.com/polkadot-js/extension'>polkadot{.js}</a>"
			  )
		    )
		    +" or <a href='https://www.subwallet.app/'>Subwallet</a>"
		    +"<br>Also you can make DOT-payment manually using QR-code"
	    );
	} else {
	    var accounts = await polkadotExtensionDapp.web3Accounts();
	    for(var l of accounts) {
		    var wal = l.meta.source.replace(/\-js$/,'');
		    if(!r[wal]) r[wal]=[];
		    r[wal].push("<option value='"+DOT.h(l.address)+"'>"+DOT.h(l.address+" "+l.meta.name)+"</option>");
	    }
	}

        var options=''; for(var wal in r) {
	    options += (wal==''? r[wal].join('') : "<optgroup label='"+DOT.h(wal)+"'>" + r[wal].join('') + "</optgroup>" );
	}
        document.getElementById('WalletID').innerHTML=options;
	DOT.alert("clear");
	DOT.alert("Found "+accounts.length+" accounts");
    },

    navigator: function(){
        var ua= navigator.userAgent;
        var tem;
        var M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
        if(/trident/i.test(M[1])){
	    tem= /\brv[ :]+(\d+)/g.exec(ua) || [];
            return 'IE '+(tem[1] || '');
	}
        if(M[1]==='Chrome'){
	    tem= ua.match(/\b(OPR|Edge)\/(\d+)/);
    	    if(tem!= null) return tem.slice(1).join(' ').replace('OPR', 'Opera');
	}
	M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
	if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
	return M[0].toLowerCase(); // .join(' | ');
    },
};

/*
// работа с адресным пространством:
// http://localhost/extension/dotpay/image/dotpay.webp
// /opt/bitnami/opencart/extension/dotpay/image/dotpay.webp
// <img src='{{ constant('HTTP_SERVER') }}extension/dotpay/image/dotpay.webp'>// Meguka password: 000000
// traffic science trophy travel sound exercise there excess myth gravity feed they

function dd(i) { return (1*i>9?i:'0'+i); }

progress2=function(now,total) { name='progress2';
    if(!idd(name)) { if(!total) return;
 newdiv("<div id='"+name+"_tab' style='width:"+Math.floor(getWinW()/2)+"px;border:1px solid #666;'>\
<div id='"+name+"_bar' style='width:0;height:10px;background-color:red;'></div>\
</div>",{cls:'progreshave',id:name});
    } else if(!total) return clean(name);
    var proc=Math.floor(1000*(now/total))/10;
    var W=1*idd(name+'_tab').style.width.replace(/[^\d]+/g,'');
    idd(name+'_bar').style.width=Math.floor(proc*(W/100))+'px';
};

ppage=function(txt) { name='ppage';
    if(!idd(name)) {
	if(txt==undefined) return;
	newdiv(txt,{cls:'progreshave',id:name});
    } else {
	if(txt==undefined) return clean(name);
	zabil(name,txt);
    }
};

// AJE ajax

// JSON multipart AJAX: https://tokmakov.msk.ru/blog/item/182
AJEinterval=false;
AJEprogress=0;
function AJE(fnn,ara) {
    if(AJEinterval) { clearInterval(AJEinterval); AJEinterval=false; }
    AJEprogress=0;
    AJEinterval=setInterval(function(){
	if(++AJEprogress>500) { clearInterval(AJEinterval); AJEinterval=false; }
	if(AJEprogress>10) progress2(AJEprogress,500);
    },50);

    ara.cookie=JA.shave_cookie;
    AJ(mainjson,function(j){
	    if(AJEinterval) { clearInterval(AJEinterval); AJEinterval=false; }
	    progress2();
	if(j===false) return salert("Server error",500);
	try{
	    j=JSON.parse(j);
	    if(j.error) {
		if(j.error == "login error") return JA.formLogin();
		return idie(  (j.message?h(j.message).replace(/\n/g,"<p>"):''),"Error: "+h(j.error) );
	    }

	    if(j.act) {
		if(j.act=='dier') return dier(j,'Server dier console');
		if(j.act=='idie') return idie(j,'Server idie console');
	    }
	    fnn(j);
	} catch(e) {
	    idie('Json Error: '+h(e.name+":"+h(e.message).replace(/\n/g,"<p>"))+"<p>"+h(e.stack).replace(/\n/g,"<p>"));
	}
    },JSON.stringify(ara));
    return false;
}

*/