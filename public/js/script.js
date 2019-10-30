$body = $("body");
	$(document).on({
		ajaxStart: function() { $body.addClass("loading"); },
		ajaxStop: function() { $body.removeClass("loading"); }  
	});
		
$(document).ready(function(){
	$('.navbar-fostrap').click(function(){
		$('.nav-fostrap').toggleClass('visible');
		$('body').toggleClass('cover-bg');
	});
	$('#pssw').val("");

	$('#select-service').hide();
		$('#demo').hide();
		$('#button').hide();
		selectedDate = '';
		selectedProviderId = '';
		selectedServiceId = '';
		selectedTime = '';
		selectedServiceDuration = 0;
		$.ajax({
			url: 'index.php?url=providers',
			type: 'get',
			success: function (response) {
				var obj = JSON.parse(response);
				for (var i=0; i<obj.length; i++) {
					var idvalue= obj[i].id;
					var namevalue = obj[i].firstName + ' ' + obj[i].lastName;
					$('#select-provider').append('<option value=' + i + '>' + namevalue + '</option>');
				}
				$('#select-provider').change(function(){
					$('#available-hours').empty();
					if ($(this).val() != '') {
						try {
							$('#select-service').show();
							var localservices = obj[$(this).val()].services;
							var selectedProvider = $(this).val();
							selectedProviderId = obj[selectedProvider].id;
							for (var i=0; i<localservices.length; i++) {
								$('#select-service').append('<option value=' + i + '>' + localservices[i].name + '</option>');
							}
							$('#select-service').change(function(){
								$('#available-hours').empty();
								if ($(this).val() != '') {
									selectedServiceId = localservices[$(this).val()].id;
									selectedServiceDuration = localservices[$(this).val()].duration;
									$('#demo').show();
									showCalendar(obj[selectedProvider].disabledDays);
								} else {
									hideCalendar();
									$('#button').hide();
								}
							});
						}
						catch(err) {
						}
					} else {
						$('#select-service').find('option').remove().end().append('<option value="">Select Service</option>').val('');
						console.log('No value');
						hideCalendar();
						$('#button').hide();
						$('#select-service').hide();
					}
				});
			}
		});
	
		function showCalendar($disabledDays) {
			$('#demo').show();
			var d = new Date();			
			$('#demo').datetimepicker({
				inline:true,
				timepicker:false,
				mask:true,
				formatDate:'yy/mm/dd',
				disabledWeekDays:$disabledDays,
				startDate:new Date(),
				minDate:0,
				monthStart:d.getMonth(),
				onSelectDate: function () {
					$('#button').hide();
					var csrf = $('#csrf').val();
					selectedDate = getFormattedDate($('#demo').datetimepicker('getValue'));
					$.ajax({
						url: "index.php?url=availabilities",
						type: "post",
						data: {
							'providerid': selectedProviderId,
							'serviceid': selectedServiceId,
							'date': selectedDate,
							'csrf': csrf,
						} ,
						success: function (response) {
							var obj = JSON.parse(response);

							if (obj.status == 'success'){
								$('#available-hours').empty();
								jQuery.each( obj.data, function( i, val ) {
									$('#available-hours').append('<span class="available-hour">' + val + '</span><br>');
								});
							} else if (obj.status == 'error'){
								alert("Error on query!");
							}
						}
					});
				},
			});
		}
		
		function hideCalendar(){
			$('#button').hide();
			$('#demo').datetimepicker('destroy');
		}
		
		function getFormattedDate(date) {
			var day = date.getDate();
			var month = date.getMonth() + 1;
			var year = date.getFullYear();
			return year + '-' + month + '-' + day;
		}

		$(document).on('click','.available-hour', function(){
				$('.available-hour').removeClass('selected-hour');
			$(this).addClass('selected-hour')
			selectedTime = $(this).text();
			$('#button').show();
		});

		$("#button").click( function(){
			var csrf = $('#csrf').val();
				$.ajax({
					url: "index.php?url=addappointment",
					type: "post",
					data: {
						'selectedDate' : selectedDate,
						'selectedTime' : selectedTime,
						'providerId' : selectedProviderId,
						'serviceId' : selectedServiceId,
						'csrf': csrf,
					},
					success: function (response) {
						var obj = JSON.parse(response);
						if (obj.status == 'success') {
							$.redirect('index.php?url=myappointments', {'message': 'Success'});
						} else if (obj.status == 'error'){
							alert("Error on query!");
						}
					}
				}); 
		});
	});