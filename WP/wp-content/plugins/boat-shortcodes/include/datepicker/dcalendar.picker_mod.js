/* -- DO NOT REMOVE --
 * jQuery DCalendar 1.1 and DCalendar Picker 1.1 plugin
 * 
 * Author: Dionlee Uy
 * Email: dionleeuy@gmail.com
 *
 * Date: Sat Mar 2 2013
 *
 * @requires jQuery
 * -- DO NOT REMOVE --
 */
if (typeof jQuery === 'undefined') { throw new Error('DCalendar.Picker: This plugin requires jQuery'); }
 
+function ($) {

	Date.prototype.getDays = function() { return new Date(this.getFullYear(), this.getMonth() + 1, 0).getDate(); };
	// Date.prototype.getWeeks = function(){ var d = new Date(this), l = this.getDays(); d.setDate(1); l += d.getDay(); return Math.ceil(l/7) };
	var months = ['January','February','March','April','May','June','July','August','September','October','November','December'],
		short_months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
		daysofweek = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

	var DCalendar = function(elem, options) {
	    this.calendar = $(elem);
		this.today = new Date();	//system date
		this.date = this.today;		//current selected date, default is today
		this.viewMode = 'days';

		
		this.minDate = this.calendar.data('mindate');
		this.maxDate = this.calendar.data('maxdate');
		this.rangeFromEl = this.calendar.data('rangefrom');
		this.rangeToEl = this.calendar.data('rangeto');

		this.options = options;
		this.selected = this.date.getMonth().toString() + "/" + this.date.getDate() + "/" + this.date.getFullYear();
		if(options.mode == 'calendar')
			this.tHead = $('<thead><tr><th id="prev">&lsaquo;</th><th colspan="5" id="currM"></th><th id="next">&rsaquo;</th></tr><tr><th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th></tr></thead>');
		else if (options.mode == 'datepicker')
			this.tHead = $('<thead><tr><th id="prev">&lsaquo;</th><th colspan="5" id="currM"></th><th id="next">&rsaquo;</th></tr><tr><th>S</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th></tr></thead>');
		this.tHead.find('#currM').text(months[this.today.getMonth()] +" " + this.today.getFullYear());
		this.calendar.prepend(this.tHead);
		var that = this;

		this.calendar.on('click', '#next', function(){ initCreate('next'); })
			.on('click', '#prev', function(){ initCreate('prev'); })
			.on('click', '#today', function(){
				that.viewMode = 'days';
				var curr = new Date(that.date),
					sys = new Date(that.today);
				if(curr.toString() != sys.toString()) { that.date = sys; that.create(that.viewMode); }
			}).on('click', '.date', function(){
				that.selected = that.date.getMonth() + 1 + "/" + $(this).text() + "/" + that.date.getFullYear();
				if(that.options.mode == 'datepicker') {
					that.calendar.find('td').removeClass('selected');
					$(this).addClass('selected');
				}
				selectDate();
				return true;
			}).on('click', '.pMDate', function(){
				that.selected = (that.date.getMonth() == 0 ? '12' : that.date.getMonth()) + "/" + $(this).text() + "/" + (that.date.getMonth() == 0 ? that.date.getFullYear()-1 : that.date.getFullYear());
				if(that.options.mode == 'datepicker') {
					that.calendar.find('td').removeClass('selected');
					$(this).addClass('selected');
				}
				selectDate();
				return true;
			}).on('click', '.nMDate', function(){
				that.selected = (that.date.getMonth()+2 == 13 ? '1' : that.date.getMonth()+2) + "/" + $(this).text() + "/" + (that.date.getMonth()+2 == 13 ? that.date.getFullYear()+1 : that.date.getFullYear());
				if(that.options.mode == 'datepicker') {
					that.calendar.find('td').removeClass('selected');
					$(this).addClass('selected');
				}
				selectDate();
				return true;
			}).on('click', '#currM', function(){
				that.viewMode = 'months';
				that.create(that.viewMode);
			}).on('click', '.month', function(e){
				that.viewMode = 'days';
				var curr = new Date(that.date), y = that.calendar.find('#currM').text();
				curr.setMonth($(e.currentTarget).attr('num'));
				that.date = curr;
				that.create(that.viewMode);
			});

		function selectDate () {
			var newDate = formatDate(that.options.format);
			var e = $.Event('selectdate',{date: newDate});
			that.calendar.trigger(e);
		}

		function formatDate (format) {
			var d = new Date(that.selected), day = d.getDate(), m = d.getMonth(), y = d.getFullYear();
			return format.replace(/(yyyy|yy|mmmm|mmm|mm|m|dd|d)/gi, function (e) {
				switch(e.toLowerCase()){
					case 'd': return day;
					case 'dd': return (day < 10 ? "0"+day: day);
					case 'm': return m+1;
					case 'mm': return (m+1 < 10 ? "0"+(m+1): (m+1));
					case 'mmm': return short_months[m];
					case 'mmmm': return months[m];
					case 'yy': return y.toString().substr(2,2);
					case 'yyyy': return y;
				}
			});
		}

		function initCreate(o){
			var curr = new Date(that.date);
			if(that.viewMode == 'days')
				o == 'next' ? curr.setMonth(curr.getMonth() + 1) : curr.setMonth(curr.getMonth() - 1);
			else
				o == 'next' ? curr.setFullYear(curr.getFullYear() + 1) : curr.setFullYear(curr.getFullYear() - 1);
			curr.setDate(1);
			that.date = curr;
			that.create(that.viewMode);
		}

		this.create(this.viewMode);
	}
	/*

	DCalendar.prototype = {

		constructor : DCalendar, 

		//setDate : function(){},

		create : function(mode){
			var that = this, cal = [], tBody = $('<tbody></tbody>'), d = new Date(that.date), days = that.date.getDays(), day = 1, nStartDate = 1, selDate = that.selected.split('/');
			that.calendar.empty();
			if(mode == "days"){
				if(that.options.mode == 'calendar')
					that.tHead = $('<thead><tr><th id="prev">&lsaquo;</th><th colspan="5" id="currM"></th><th id="next">&rsaquo;</th></tr><tr><th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th></tr></thead>');
				else if (that.options.mode == 'datepicker')
					that.tHead = $('<thead><tr><th id="prev">&lsaquo;</th><th colspan="5" id="currM"></th><th id="next">&rsaquo;</th></tr><tr><th>S</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th></tr></thead>');
				that.tHead.find('#currM').text(months[that.date.getMonth()] +" " + that.date.getFullYear());
				that.calendar.append(that.tHead);
				for(var i=1; i<=6; i++){
					var temp = [$('<td></td>'),$('<td></td>'),$('<td></td>'),$('<td></td>'),$('<td></td>'),$('<td></td>'),$('<td></td>')];

					while(day<=days){
						d.setDate(day);
						var dayOfWeek = d.getDay();
						if(day == that.today.getDate() && d.getMonth() == that.today.getMonth() && d.getFullYear() == that.today.getFullYear()) {
							temp[dayOfWeek].attr('id', 'currDay');
						} else if(that.options.mode == 'datepicker' && (day == selDate[1] && d.getMonth() == (selDate[0]-1) && d.getFullYear() == selDate[2])){
							temp[dayOfWeek].addClass('selected');
						}
						if(i == 1 && dayOfWeek == 0){
							break;
						} else if(dayOfWeek < 6){
							temp[dayOfWeek].html('<span>'+(day++)+'</span>').addClass('date');
						} else {
							temp[dayOfWeek].html('<span>'+(day++)+'</span>').addClass('date');
							break;
						}
					}
					if(i == 1 || i > 4){
						var p = new Date(that.date);
						p.setMonth(p.getMonth()+(i==1?-1:1));
						var pDays = p.getDays();
						for(var a=(i==1?6:0); (i==1?(a>=0):(a<=6)); (i==1?a--:a++)){
							if(temp[a].text() == ''){
								temp[a].html('<span>'+((i==1?pDays--:nStartDate++))+'</span>').addClass((i==1?'pMDate':'nMDate'));
								if(that.options.mode == 'datepicker' && ((i==1?(pDays+1):(nStartDate-1)) == selDate[1] && p.getMonth() == (selDate[0]-1) && p.getFullYear() == selDate[2])){
									temp[a].addClass('selected');
								}
							}
						}
					}
					cal.push(temp);
				}

				$.each(cal, function(i, v){
					var row = $('<tr></tr>'), l = v.length;
					for(var i=0;i<l;i++){ row.append(v[i]); }
					tBody.append(row);
				});

				var sysDate = "Today: " + daysofweek[that.today.getDay()] + ", "+ months[that.today.getMonth()] + " " + that.today.getDate() + ", " + that.today.getFullYear();
				tBody.append('<tr><td colspan="7" id="today">'+sysDate+'</td></tr>').appendTo(that.calendar);
			} else {
				this.tHead = $('<thead><tr><th id="prev">&lsaquo;</th><th colspan="2" id="currM"></th><th id="next">&rsaquo;</th></tr>');
				that.tHead.find('#currM').text(that.date.getFullYear());
				that.tHead.appendTo(that.calendar);
				var currI = 0;
				for (var i = 0; i < 3; i++) {
					var row = $('<tr></tr>');
					for (var x = 0; x < 4; x++) {
						var col = $('<td align="center"></td>');
						var m = $('<span class="month" num="'+currI+'">'+short_months[currI]+'</span>');
						col.append(m).appendTo(row);
						currI++;
					}
					tBody.append(row);
				}
				var sysDate = "Today: " + daysofweek[that.today.getDay()] + ", "+ months[that.today.getMonth()] + " " + that.today.getDate() + ", " + that.today.getFullYear();
				tBody.append('<tr><td colspan="4" id="today">'+sysDate+'</td></tr>').appendTo(that.calendar);
			}
		},

		disabledDate: function (date) {
			var that = this, rangeFrom = null, rangeTo = null, rangeMin = null, rangeMax = null, min = null, max = null,
				now = new Date(), today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
alert("hello");
			if (that.minDate) min = that.minDate === "today" ? today : new Date(that.minDate);
			if (that.maxDate) max = that.maxDate === "today" ? today : new Date(that.maxDate);

			if (that.rangeFromEl) {
				var fromEl = $(that.rangeFromEl),
					fromData = fromEl.data(DCAL_DATA);
					fromFormat = fromData.options.format,
					fromVal = fromEl.val();

				rangeFrom = that.reformatDate(fromVal, fromFormat).date;
				rangeMin = fromData.minDate === "today" ? today : new Date(fromData.minDate);
			}

			if (that.rangeToEl) {
				var toEl = $(that.rangeToEl),
					toData = toEl.data(DCAL_DATA);
					toFormat = toData.options.format,
					toVal = toEl.val();

				rangeTo = that.reformatDate(toVal, toFormat).date;
				rangeMax = toData.maxDate === "today" ? today : new Date(toData.maxDate);
			}

			return (min && date < min) || (max && date > max) || (rangeFrom && date < rangeFrom) || (rangeTo && date > rangeTo) ||
				(rangeMin && date < rangeMin) || (rangeMax && date > rangeMax);
		},
	}
*/
DCalendar.prototype = {
	constructor : DCalendar,
	/* Parses date string using default or specified format. */
	reformatDate : function (date, dateFormat) {
		var that = this,
			format = typeof dateFormat === 'undefined' ? that.options.format : dateFormat,
			dayLength = (format.match(/d/g) || []).length,
			monthLength = (format.match(/m/g) || []).length,
			yearLength = (format.match(/y/g) || []).length,
			isFullMonth = monthLength == 4,
			isMonthNoPadding = monthLength == 1,
			isDayNoPadding = dayLength == 1,
			lastIndex = date.length,
			firstM = format.indexOf('m'), firstD = format.indexOf('d'), firstY = format.indexOf('y'),
			month = '', day = '', year = '';

		// Get month on given date string using the format (default or specified)
		if(isFullMonth) {
			var monthIdx = -1;
			$.each(months, function (i, m) { if (date.indexOf(m) >= 0) monthIdx = i; });
			month = months[monthIdx];
			format = format.replace('mmmm', month);
			firstD = format.indexOf('d');
			firstY = firstY < firstM ? format.indexOf('y') : format.indexOf('y', format.indexOf(month) + month.length);
		} else if (!isDayNoPadding && !isMonthNoPadding || (isDayNoPadding && !isMonthNoPadding && firstM < firstD)) {
			month = date.substr(firstM, monthLength);
		} else {
			var lastIndexM = format.lastIndexOf('m'),
				before = format.substring(firstM - 1, firstM),
				after = format.substring(lastIndexM + 1, lastIndexM + 2);

			if (lastIndexM == format.length - 1) {
				month = date.substring(date.indexOf(before, firstM - 1) + 1, lastIndex);
			} else if (firstM == 0) {
				month = date.substring(0, date.indexOf(after, firstM));
			} else {
				month = date.substring(date.indexOf(before, firstM - 1) + 1, date.indexOf(after, firstM + 1));
			}
		}

		// Get date on given date string using the format (default or specified)
		if (!isDayNoPadding && !isMonthNoPadding || (!isDayNoPadding && isMonthNoPadding && firstD < firstM)) {
			day = date.substr(firstD, dayLength);
		} else {
			var lastIndexD = format.lastIndexOf('d');
				before = format.substring(firstD - 1, firstD),
				after = format.substring(lastIndexD + 1, lastIndexD + 2);

			if (lastIndexD == format.length - 1) {
				day = date.substring(date.indexOf(before, firstD - 1) + 1, lastIndex);
			} else if (firstD == 0) {
				day = date.substring(0, date.indexOf(after, firstD));
			} else {
				day = date.substring(date.indexOf(before, firstD - 1) + 1, date.indexOf(after, firstD + 1));
			}
		}

		// Get year on given date string using the format (default or specified)
		if (!isMonthNoPadding && !isDayNoPadding || (isMonthNoPadding && isDayNoPadding && firstY < firstM && firstY < firstD)
			|| (!isMonthNoPadding && isDayNoPadding && firstY < firstD) || (isMonthNoPadding && !isDayNoPadding && firstY < firstM)) {
			year = date.substr(firstY, yearLength);
		} else {
			var before = format.substring(firstY - 1, firstY);
			year = date.substr(date.indexOf(before, firstY - 1) + 1, yearLength);
		}

		return { m: month, d: day, y: year, date: isNaN(parseInt(month)) ? new Date(month + " " + day + ", " + year) : new Date(year, month - 1, day) };
	},
	/* Returns formatted string representation of selected date */
	formatDate : function (format) {
		var d = new Date(this.selected), day = d.getDate(), m = d.getMonth(), y = d.getFullYear();
		return format.replace(/(yyyy|yy|mmmm|mmm|mm|m|dd|d)/gi, function (e) {
			switch(e.toLowerCase()){
				case 'd': return day;
				case 'dd': return (day < 10 ? "0"+day: day);
				case 'm': return m+1;
				case 'mm': return (m+1 < 10 ? "0"+(m+1): (m+1));
				case 'mmm': return short_months[m];
				case 'mmmm': return months[m];
				case 'yy': return y.toString().substr(2,2);
				case 'yyyy': return y;
			}
		});
	},
	/* Selects date and trigger event (for other actions - if specified) */
	selectDate : function () {
		var that = this,
			newDate = that.formatDate(that.options.format),
			e = $.Event('dateselected', {date: newDate});

		that.elem.trigger(e);
	},
	/* Determines if date is disabled */
	disabledDate: function (date) {
		var that = this, rangeFrom = null, rangeTo = null, rangeMin = null, rangeMax = null, min = null, max = null,
			now = new Date(), today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

		if (that.minDate) min = that.minDate === "today" ? today : new Date(that.minDate);
		if (that.maxDate) max = that.maxDate === "today" ? today : new Date(that.maxDate);

		if (that.rangeFromEl) {
			var fromEl = $(that.rangeFromEl),
				fromData = fromEl.data(DCAL_DATA);
				fromFormat = fromData.options.format,
				fromVal = fromEl.val();

			rangeFrom = that.reformatDate(fromVal, fromFormat).date;
			rangeMin = fromData.minDate === "today" ? today : new Date(fromData.minDate);
		}

		if (that.rangeToEl) {
			var toEl = $(that.rangeToEl),
				toData = toEl.data(DCAL_DATA);
				toFormat = toData.options.format,
				toVal = toEl.val();

			rangeTo = that.reformatDate(toVal, toFormat).date;
			rangeMax = toData.maxDate === "today" ? today : new Date(toData.maxDate);
		}

		return (min && date < min) || (max && date > max) || (rangeFrom && date < rangeFrom) || (rangeTo && date > rangeTo) ||
			(rangeMin && date < rangeMin) || (rangeMax && date > rangeMax);
	},
	/* Gets list of months (for month view) */
	getMonths : function () {
		var that = this,
			currentYear = that.today.getFullYear(),
			currentMonth = that.today.getMonth();

		if(that.viewMode !== 'days') return;
		var cal = that.calendar;
			curr = cal.find('.calendar-dates'),
			dayLabel = cal.find('.calendar-labels'),
			currMonth = cal.find('.calendar-curr-month'),
			container = cal.find('.calendar-date-holder'),
			cElem = curr.clone(),
			rows = [], cells = [], count = 0;

		that.viewMode = 'months';
		currMonth.text(that.date.getFullYear());
		dayLabel.addClass('invis');
		for (var i = 1; i < 4; i++) {
			var row = [$("<span class='date month'></span>"), $("<span class='date month'></span>"), $("<span class='date month'></span>"), $("<span class='date month'></span>")];
			for (var a = 0; a < 4; a++) {
				row[a].html("<a href='javascript:void(0);'>" + short_months[count] + "</a>").attr('data-month', count);
				count++;
			}
			rows.push(row);
		}
		$.each(rows, function(i, v){
			var row = $('<span class="cal-row"></span>'), l = v.length;
			for(var i = 0; i < l; i++) { row.append(v[i]); }
			cells.push(row);
		});
		container.parent().height(container.parent().outerHeight(true));
		cElem.empty().append(cells).addClass('months load').appendTo(container);
		curr.addClass('hasmonths');
		setTimeout(function () { cElem.removeClass('load'); }, 10);
		setTimeout(function () { curr.remove(); }, 300);
	},
	/* Gets days for month of 'newDate'*/
	getDays : function (newDate, callback) {
		var that = this,
			ndate = new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate()),
			today = new Date(that.today.getFullYear(), that.today.getMonth(), that.today.getDate()),
			days = ndate.getDays(), day = 1,
			d = new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate()),
			nmStartDay = 1, weeks = [], dates = [];

		for(var i = 1; i <= 6; i++){
			var week = [$('<span class="date"></span>'), $('<span class="date"></span>'), $('<span class="date"></span>'),
						$('<span class="date"></span>'), $('<span class="date"></span>'), $('<span class="date"></span>'),
						$('<span class="date"></span>')];

			while(day <= days) {
				d.setDate(day);
				var dayOfWeek = d.getDay();

				if (d.getTime() == today.getTime()) week[dayOfWeek].addClass('current');

				if (that.disabledDate(d)) week[dayOfWeek].addClass('disabled');

				if(i === 1 && dayOfWeek === 0){
					break;
				} else if (dayOfWeek < 6) {
					if (d.getTime() == that.selected.getTime()) week[dayOfWeek].addClass('selected');

					week[dayOfWeek].html('<a href="javascript:void(0);">' + (day++) + '</a>');
				} else {
					if (d.getTime() == that.selected.getTime()) week[dayOfWeek].addClass('selected');

					week[dayOfWeek].html('<a href="javascript:void(0);">' + (day++) + '</a>');
					break;
				}
			}
			/* For days of previous and next month */
			if (i === 1 || i > 4) {
				// First week
				if (i === 1) {
					var pmDate = new Date(newDate.getFullYear(), newDate.getMonth() - 1, 1);
					var pMonth = pmDate.getMonth(), pDays = 0;
					pDays = pmDate.getDays();
					for (var a = 6; a >= 0; a--) {
						if (week[a].text() !== '') continue;

						pmDate.setDate(pDays);
						week[a].html('<a href="javascript:void(0);">' + (pDays--) + '</a>').addClass('pm');

						if (that.disabledDate(pmDate)) week[a].addClass('disabled');

						if (pmDate.getTime() == that.selected.getTime()) week[a].addClass('selected');
						if (pmDate.getTime() == today.getTime()) week[a].addClass('current');
					}
				} 
				// Last week
				else if (i > 4) {
					var nmDate = new Date(d.getFullYear(), d.getMonth() + 1, 1);
					for (var a = 0; a <= 6; a++) {
						if (week[a].text() !== '') continue;

						nmDate.setDate(nmStartDay);
						week[a].html('<a href="javascript:void(0);">' + (nmStartDay++) + '</a>').addClass('nm');

						if (that.disabledDate(nmDate)) week[a].addClass('disabled');

						if (nmDate.getTime() == that.selected.getTime()) week[a].addClass('selected');
						if (nmDate.getTime() == today.getTime()) week[a].addClass('current');
					}
				}
			}
			weeks.push(week);
		}
		$.each(weeks, function(i, v){
			var row = $('<span class="cal-row"></span>'), l = v.length;
			for(var i = 0; i < l; i++) { row.append(v[i]); }
			dates.push(row);
		});
		callback(dates);
	},
	/* Sets current view based on user interaction (on arrows) */
	getNewMonth : function (dir, isTrigger) {
		var that = this,
			cal = that.calendar;
			curr = cal.find('.calendar-dates:not(.left):not(.right)'),
			lblTodayDay = cal.find('.calendar-dayofweek'),
			lblTodayMonth = cal.find('.calendar-month'),
			lblTodayDate = cal.find('.calendar-date'),
			lblTodayYear = cal.find('.calendar-year'),
			lblMonth = cal.find('.calendar-curr-month'),
			container = cal.find('.calendar-date-holder');

		if (that.viewMode === 'days') {
			if (isTrigger) {
				that.date.setDate(1);
				that.date.setMonth(that.date.getMonth() + ( dir === 'right' ? 1 : -1));
			}
			if(isTrigger || that.options.mode === 'calendar' || curr.hasClass('months')) {
				that.getDays(that.date, function (dates) {
					if (isTrigger) {
						var cElem = curr.clone();
						cElem.addClass(dir).empty().append(dates)[dir == 'left' ? 'prependTo' : 'appendTo'](container);
						setTimeout(function() {
							curr.addClass(dir == 'left' ? 'right' : 'left');
							cElem.removeClass(dir);
							setTimeout(function () { cal.find('.calendar-dates.'+(dir == 'left' ? 'right' : 'left')+'').remove(); }, 300);
						}, 10);
					} else {
						if (curr.hasClass('months')) {
							var cElem = curr.clone();
							$('.calendar-labels').removeClass('invis');
							cElem.empty().append(dates).addClass('hasmonths').appendTo(container);
							curr.addClass('load');
							setTimeout(function () { cElem.removeClass('hasmonths'); }, 10);
							container.parent().removeAttr('style');
							setTimeout(function () {
								cElem.removeClass('months');
								setTimeout(function () { cal.find('.calendar-dates.months').remove(); }, 300);
							}, 10);
						} else {
							curr.append(dates);
						}
					}
				});
			}
			
			lblMonth.text(months[that.date.getMonth()] + ' ' + that.date.getFullYear());
			
			if (!isTrigger && !curr.hasClass('months')) {
				lblTodayDay.text(short_days[that.today.getDay()]);
				lblTodayMonth.text(short_months[that.today.getMonth()]);
				lblTodayDate.text(that.today.getDate());
				lblTodayYear.text(that.today.getFullYear());
			}
		} else {
			that.date.setYear(that.date.getFullYear() + ( dir === 'right' ? 1 : -1))
			lblMonth.text(that.date.getFullYear());
		}
	},
	/* Sets current view to selected date */
	selectedView : function () {
		var that = this,
			cal = that.calendar;
			curr = cal.find('.calendar-dates:eq(0)'),
			lblMonth = cal.find('.calendar-curr-month'),
			lblDays = cal.find('.calendar-labels');

		that.getDays(that.selected, function (dates) {
			curr.html(dates);
		});

		lblMonth.text(months[that.selected.getMonth()] + ' ' + that.selected.getFullYear());
		lblDays.removeClass('invis');
		that.viewMode = 'days';
	},
	/* Creates components for the calendar */
	create : function(){
		var that = this,
			mode = that.options.mode,
			theme = that.options.theme,
			overlay = $('<div class="calendar-overlay"></div>'),
			wrapper = $('<div class="calendar-wrapper load"></div>'),
			cardhead = $('<section class="calendar-head-card"><span class="calendar-year"></span><span class="calendar-date-wrapper" title="Select current date."><span class="calendar-dayofweek"></span>, <span class="calendar-month"></span> <span class="calendar-date"></span></span></section>'),
			container = $('<div class="calendar-container"></div>'),
			calhead = $('<section class="calendar-top-selector"><span class="calendar-prev">&lsaquo;</span><span class="calendar-curr-month"></span><span class="calendar-next">&rsaquo;</span></section>'),
			datesgrid = $('<section class="calendar-grid">'
						+ '<div class="calendar-labels"><span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span></div>'
						+ '<div class="calendar-date-holder"><section class="calendar-dates"></section></div></section>');

		calhead.appendTo(container);
		datesgrid.appendTo(container);

		overlay.click(function (e) { that.hide(); });
		wrapper.click(function (e) { e.stopPropagation(); });

		wrapper.append(cardhead).append(container).appendTo(mode === 'calendar' ? that.elem : overlay);
		that.calendar = mode === 'calendar' ? that.elem : wrapper;

		switch(theme) {
			case 'red':
			case 'blue':
			case 'green':
			case 'purple':
			case 'indigo':
			case 'teal':
				wrapper.attr('data-theme', theme);
			break;
			default:
				wrapper.attr('data-theme', $.fn.dcalendar.defaults.theme);
			break;
		}

		if(mode !== 'calendar') { 
			wrapper.addClass('picker');
			overlay.appendTo('body');
		}
	},
	/* Shows the calendar (date picker) */
	show : function () {
		$('body').attr('datepicker-display', 'on');
		this.date = new Date(this.selected.getFullYear(), this.selected.getMonth(), this.selected.getDate());
		this.selectedView();
		this.calendar.parent().fadeIn('fast');
		this.calendar.removeClass('load');
	},
	/* Hides the calendar (date picker) */
	hide : function (callback) {
		var that = this;
		that.calendar.addClass('load');
		that.calendar.parent().fadeOut(function () {
			$('body').removeAttr('datepicker-display');
			if(callback) callback();
			if(that.elem.is('input')) that.elem.focus();
		});
	}
};


	/* DEFINITION FOR DCALENDAR */
/*	$.fn.dcalendar = function(opts){
		return $(this).each(function(index, elem){
			var that = this;
 			var $this = $(that),
 				data = $(that).data('dcalendar'),
 				options = $.extend({}, $.fn.dcalendar.defaults, $this.data(), typeof opts == 'object' && opts);
 			if(!data){
 				$this.data('dcalendar', (data = new DCalendar(this, options)));
 			}
 			if(typeof opts == 'string') data[opts]();
		});
	}

	$.fn.dcalendar.defaults = {
		mode : 'calendar',
		format: 'mm/dd/yyyy',
	};

	$.fn.dcalendar.Constructor = DCalendar;

	/* DEFINITION FOR DCALENDAR PICKER */
/*	$.fn.dcalendarpicker = function(opts){
		return $(this).each(function(){
			var that = $(this);
			var cal = $('<table class="calendar"></table>'), hovered = false, selectedDate = false;
			that.wrap($('<div class="datepicker" style="display:inline-block;position:relative;"></div>'));
			cal.css({
				position:'absolute',
				left:0, display:'none',
				'box-shadow':'0 0 4px rgba(0,0,0,0.15)',
				width:'220px',
			}).appendTo(that.parent());
			if(opts){
				opts.mode = 'datepicker';
				cal.dcalendar(opts);
			}
			else
				cal.dcalendar({mode: 'datepicker'});
			cal.hover(function(){
				hovered = true;
			}, function(){
				hovered = false;
			}).on('click', function(){
				if(!selectedDate)
					that.focus();
				else {
					selectedDate = false;
					$(this).hide();
				}
			}).on('selectdate', function(e){
				that.val(e.date);
			    that.trigger($.Event('dateselected',{date: e.date, elem: that}));
				selectedDate = true;
			});
			that.on('keydown', function(e){ if(e.which) return false; })
				.on('focus', function(){
					$('.datepicker').find('.calendar').not(cal).hide();
					cal.show();
				})
				.on('blur', function(){ if(!hovered) cal.hide(); });
		});
	}

}(jQuery);*/

	/* DEFINITION FOR DCALENDAR */
	$.fn.dcalendar = function(opts){
		return $(this).each(function(index, elem){
			var that = this;
 			var $this = $(that),
 				data = $(that).data(DCAL_DATA),
 				options = $.extend({}, $.fn.dcalendar.defaults, $this.data(), typeof opts === 'object' && opts);
 			if(!data){
 				$this.data(DCAL_DATA, (data = new DCalendar(this, options)));
 			}
 			if(typeof opts === 'string') data[opts]();
		});
	};

	$.fn.dcalendar.defaults = {
		mode : 'calendar',
		format: 'mm/dd/yyyy',
		theme: 'blue',
		readOnly: true
	};

	$.fn.dcalendar.Constructor = DCalendar;

	/* DEFINITION FOR DCALENDAR PICKER */
	$.fn.dcalendarpicker = function(opts){
		return $(this).each(function(){
			var that = $(this);

			if(opts){
				opts.mode = 'datepicker';
				that.dcalendar(opts);
			} else{
				that.dcalendar({mode: 'datepicker'});
			}

			that.on('click', function (e) {
				var cal = that.data(DCAL_DATA);
				cal.show();
				this.blur();
			}).on('dateselected', function (e) {
				var cal = that.data(DCAL_DATA);
				that.val(e.date).trigger('onchange');
				cal.hide(function () {
					that.trigger($.Event('datechanged', {date: e.date}));
				});				
			}).on('keydown', function(e){
				if(ex_keys.indexOf(e.which) < 0 && that.data(DCAL_DATA).options.readOnly) return false; 
			});
			$(document).on('keydown', function (e) {
				if(e.keyCode != 27) return;
				that.data(DCAL_DATA).hide();
			});
		});
	};
}(jQuery);