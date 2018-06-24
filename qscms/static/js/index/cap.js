Captcha = {
	urlCheck: webSu+'images/vcode3.php',
	toPutCaptcha: '#captcha',
	load: function() {
		$.get(Captcha.urlCheck).then(function(data) {
			//Captcha.setId(data);
			Captcha.draw(data);
			return data;
		});
	},
	setId: function(data) {
		$(Captcha.toPutCaptcha).attr('data-id', data.id);
	},
	draw: function(data) {
		var captchaData = data.captcha;
		var canvas = $(Captcha.toPutCaptcha)[0];
		var context = canvas.getContext('2d');
		context.clearRect(0, 0, 160, 100);
		var x = -1,
		y = -1;
		captchaData.split('\n').forEach(function(line) {
			y++;
			x = -1;
			line.split('').forEach(function(char) {
				x++;
				if (char == '#') {
					context.fillRect(x, y, 1, 1);
				}
			});
		});
	}
};