jQuery(function($){$(document).ready( function() {
 console.log('connect rating.js');
 // выбираем элементы, классы которых начинаются на brpv_raiting_star_
 $('[class ^= brpv_raiting_star_]').each(function() { 
	var curpostid = $(this).children(".hidden").attr('postid');
	console.log('curpostid = '+curpostid);	
	output_stars(curpostid);
 }); 
 
 function output_stars (postId) { // ф-я вывода звед
	console.log('стартовала output_stars');	
	console.log('output_stars: postId = '+postId);
	var zapros = '.brpv_raiting_star_'+postId; 
	var total_reiting = $(zapros).children(".hidden").attr('ratingvalue');
	var star_widht = total_reiting*17 ; // ширина одной звездочки
	$(zapros).children('div').children('.raiting_votes').width(star_widht); // выводим звездочки
	$(zapros).children('div').children('.raiting_info span').append(total_reiting); // выводим числовое значение рейтинга
	
	he_voted = $.cookies.get('article'+postId); // проверяем есть ли кука?
	if (he_voted == null) { console.log('output_stars: куки нет');	
		 /* наведение на звездочки */
		$(zapros+' .raiting').hover(function() {
			$(zapros).children('div').children('.raiting_votes, .raiting_hover').toggle();
		},
		function() {
			$(zapros).children('div').children('.raiting_votes, .raiting_hover').toggle();
		});
	}
 } 
 
 /* наведение на звездочки */
 var margin_doc = $(".raiting").offset(); // С помощью этих функций, можно узнавать координаты элемента на странице. Кроме этого, с помощью offset(), можно изменить координаты элемента. Имеется несколько вариантов использования функций.
 $(".raiting").mousemove(function(e) {
	var widht_votes = e.pageX - margin_doc.left;
	if (widht_votes == 0) widht_votes = 1;
	user_votes = Math.ceil(widht_votes/17);  
	// обратите внимание переменная user_votes должна задаваться без var, т.к. в этом случае она будет глобальной и мы сможем к ней обратиться из другой ф-ции (нужна будет при клике на оценке).
	$('.raiting_hover').width(user_votes*17);
 });
 /* end наведение на звездочки */
 
 $('.raiting').click(function(){
	console.log('click .raiting');	
	var postId = $(this).siblings('.hidden').attr('postid'); // получаем id поста, которому ставят оценку
	he_voted = $.cookies.get('article'+postId); // проверяем есть ли кука?
	if (he_voted !== null) {console.log('Юзер уже голосовал. клик отменен!'); return;}
	
	var zapros = '.brpv_raiting_star_'+postId;
	$(zapros).children(".raiting").unbind();
	$(zapros).children('div').children('.raiting_hover').hide();
	$(zapros).children('div').children('.raiting_votes, .raiting_hover').hide();

	$.ajax({
		type: "GET",
		dataType : "json",
		url:  brpvajax.brpvajaxurl,
		data: {
			action: 'brpv_ajax_func',
			user_votes: user_votes,
			postId: postId
		}, 
		/* 
		*	brpv_ajax_php_func - функция в php файле, в которой происходит 
		*	обработка аякс запроса.
		* 	data, а точнее $_REQUEST['data'] хранит массив с именами
		*	и значениями полей формы
		*/
			beforeSend : function() {
				// происходит непосредственно перед отправкой запроса на сервер.
				console.log('отработала beforeSend');
			},
			error : function(resp) {
				console.table(resp);
				// происходит в случае неудачного выполнения запроса.
				console.log('отработала error');
			},
			success : function(answer) {
				// происходит в случае удачного завершения запроса
				console.table(answer); /* ОТЛАДОЧНАЯ ИНФОРМАЦИЯ. Что вернулось? */				
				console.log('отработала success');
				if (answer['status'] == 'success') {
					console.log('статус ответа от php = "success"');
					$.cookies.set('article'+postId, 123, {hoursToLive: 1}); // создаем куку о голосовании
					var zapros = '.brpv_raiting_star_'+postId;
					$(zapros).children('div').children('.raiting_info span').text(answer['total_rating_new']); // выводим числовое значение рейтинга
					var star_widht = answer['total_rating_new']*17 ; // ширина одной звездочки					
					$(zapros).children(".raiting").unbind();
					$(zapros).children('div').children('.raiting_votes').toggle();
					$(zapros).children('div').children('.raiting_votes').width(star_widht); // выводим звездочки	
				}				
			},
			complete : function() {
				// происходит в случае любого завершения запроса
				console.log('отработала complete');				
			}		
	});
	return;
 });

})}); // end jQuery