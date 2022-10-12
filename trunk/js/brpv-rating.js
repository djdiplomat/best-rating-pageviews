jQuery(function($){$(document).ready( function() {
	console.log('Best Rating & Pageviews connect rating.js');
 
	$('.brpv_raiting_icon').click(function() {
		console.log('click .brpv_raiting_icon');

		var postId = $(this).attr('post-id'); // получаем id поста, которому ставят оценку
		var he_voted = $.cookies.get('article'+postId); // проверяем есть ли кука?

		if (he_voted !== null) {console.log('Юзер уже голосовал. клик отменен!'); return;}
		
		var user_votes = $(this).attr('data-rating');
		$(this).unbind();
		$(this).siblings().unbind();

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
						var zapros = '#brpv_raiting_star_'+postId;
						$(zapros).children('div').children('.brpv_raiting_info span').text(answer['total_rating_new']); // выводим числовое значение рейтинга
						
						var zapros = '#half_'+postId;
						var rating_value = parseFloat(answer['total_rating_new'])
						var p = (rating_value - parseInt(rating_value)).toFixed(2);
						p = p*100;						
						$(zapros).children("rect").eq(1).attr('x', p+'%');
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