var modulePath = baseUri+'/modules/alexlogin/';
var ajaxPath = modulePath+'ajax.php';

function showLoginPopup(){
	$('.alexlogin-register-wrap').slideUp();
	$('.alexlogin-login-wrap').slideToggle();	
}

function showRegisterPopup(){
	$('.alexlogin-login-wrap, .alexlogin-register-wrap').slideToggle();
}

/*
* ajax request with JSONP
*/
function ajaxJsonpMethod(dataURL, postData, calBackfun){
	$.ajax({
		url: dataURL,			
		jsonp: "callback",
		dataType: "jsonp",
		data: postData,
		success: function( response ) {
			calBackfun(response);
		}
	});
}

function alexRegister(){
	var reg_name = $('#reg_name').val(),
	reg_password = $('#reg_password').val(),
	reg_cnf_password = $('#reg_cnf_password').val(),
	reg_surname = $('#reg_surname').val(),
	reg_email = $('#reg_email').val(),
	reg_term_condition = $('#reg_term_condition').is(':checked'),
	emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
	error = 0,
	err = '';

	if(reg_name==''){
		err += '<li>'+err_reg_name+'</li>';
		error = error+1;
	}
	if(reg_password==''){
		err += '<li>'+err_reg_password+'</li>';
		error = error+1;
	}
	if(reg_cnf_password==''){
		err += '<li>'+err_reg_cnf_password+'</li>';
		error = error+1;
	}
	if(reg_password!=reg_cnf_password){
		err += '<li>'+err_reg_valid_password+'</li>';
		error = error+1;
	}
	if(reg_surname==''){
		err += '<li>'+err_reg_surname+'</li>';
		error = error+1;
	}
	if(reg_email==''){
		err += '<li>'+err_reg_email+'</li>';
		error = error+1;
	}else if( !emailReg.test(reg_email) ) {
		err += '<li>'+err_reg_valid_email+'</li>';
		error = error+1;
	}
	if(!reg_term_condition){
		err += '<li>'+err_reg_term_condition+'</li>';
		error = error+1;
	}
	
	if(error > 0){
		$('#ajaxlogin-erro').html('<div class="alert alert-danger"><ol>'+err+'</ol></div>');
	}else{
		$('#ajaxlogin-erro').html('');
		var postData = [];		
		postData.push({name: 'reg_name', value: reg_name });	
		postData.push({name: 'reg_password', value: reg_password });	
		postData.push({name: 'reg_surname', value: reg_surname });	
		postData.push({name: 'reg_email', value: reg_email });		
		postData.push({name: 'task', value: 'register' });	
		$('#btn-register').addClass('btn-loading').attr("disabled", true);
		ajaxJsonpMethod(ajaxPath, postData, alexRegisterSuccess);
	}
}

function alexRegisterSuccess(data){
	$('#btn-register').removeClass('btn-loading').attr("disabled", false);
	if(data.success==1){
		parent.location.reload();
	}else{	
		var err = ''
		$.each( data.errors, function( key, value ) {			
			err += '<li>'+value+'</li>';
		});	
		if(err!='')	
			$('#ajaxlogin-erro').html('<div class="alert alert-danger"><ol>'+err+'</ol></div>');
	}
}

function alexLogin(){
	var log_email = $('#log_email').val(),
	log_password = $('#log_password').val(),		
	emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
	error = 0,
	err = '';

	if(log_email==''){
		err += '<li>'+err_log_email+'</li>';
		error = error+1;
	}else if( !emailReg.test(log_email) ) {
		err += '<li>'+err_log_valid_email+'</li>';
		error = error+1;
	}

	if(log_password==''){
		err += '<li>'+err_log_password+'</li>';
		error = error+1;
	}
	if(error > 0){
		$('#ajaxlogin-erro').html('<div class="alert alert-danger"><ol>'+err+'</ol></div>');
	}else{
		$('#ajaxlogin-erro').html('');
		var postData = [];		
		postData.push({name: 'log_email', value: log_email });	
		postData.push({name: 'log_password', value: log_password });	
		postData.push({name: 'task', value: 'login' });	
		$('#btn-login').addClass('btn-loading').attr("disabled", true);
		ajaxJsonpMethod(ajaxPath, postData, alexLoginSuccess);
	}
}
function alexLoginSuccess(data){
	$('#btn-login').removeClass('btn-loading').attr("disabled", false);
	if(data.success==1){
		parent.location.reload();
	}else{	
		var err = ''
		$.each( data.errors, function( key, value ) {			
			err += '<li>'+value+'</li>';
		});	
		if(err!='')	
			$('#ajaxlogin-erro').html('<div class="alert alert-danger"><ol>'+err+'</ol></div>');
	}
}

/*
var mouse_is_inside = false;
$(document).ready(function()
{
    $('.alexlogin-form-wrap').hover(function(){ 
        mouse_is_inside=true; 
    }, function(){ 
        mouse_is_inside=false; 
    });

    $("body").click(function(){ 
        if(! mouse_is_inside) $('.alexlogin-form-wrap').hide();
    });
});
*/