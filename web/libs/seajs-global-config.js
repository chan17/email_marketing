seajs.config({
	plugins: ['shim'],

	alias: {
		'jquery': {
		  src: 'jquery/jquery-1.8.3.min.js',
		  exports: 'jQuery'
		},
		'$' : {
		  src: 'jquery/jquery-1.8.3.min.js',
		  exports: 'jQuery'
		},
		'$-debug' : {
		  src: 'jquery/jquery-1.8.3.js',
		  exports: 'jQuery'
		},
		'bootstrap': {
		  src: 'bootstrap/3.0.0/bootstrap.js'
		},
		'bootstrap.validator' : 'bootstrap/3.0.0/validator',
		'arale.widget' : 'arale/widget/1.0.3/widget',
		'arale.validator' : 'arale/validator/0.9.1/validator',
		'arale.validator-core' : 'arale/validator/0.9.1/core',
		'arale.tip' : 'arale/tip/1.0.0/tip',
		'arale.atip' : 'arale/tip/1.0.0/atip',
		'arale.tabs' : 'arale/switchable/0.9.12/tabs',
		'toastr': {
		  src: 'toastr/1.2.2/toastr.js'
		},
	},

	debug : app.debug
})