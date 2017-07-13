Tea.View.scope(function () {
	var now = new Date();

	this.timestamp = Math.floor(now.getTime()/1000);
	this.date = now.format("Y-m-d H:i:s");
	this.timeUnit = "s";

	Tea.delay(function () {
		this.timeToDate();
		this.dateToTime();
	});

	this.timeToDate = function () {
		if (this.timestamp == null) {
			this.timestamp = "";
		}
		if (this.timestamp.toString().split(".")[0].length <= 10) {
			this.dateFromTime = new Date(this.timestamp * 1000).format("Y-m-d H:i:s");
			this.timeUnit = "s";
		}
		else {
			this.dateFromTime = new Date(this.timestamp).format("Y-m-d H:i:s");
			this.timeUnit = "ms";
		}
		if (this.dateFromTime.length == 0) {
			this.dateFromTime = "-";
		}
	};

	this.randTime = function () {
		this.timestamp = Math.ceil(Math.random() * (new Date()).getTime());
		this.timeToDate();
	};

	this.dateToTime = function () {
		Tea.action(".dateToTime")
			.params({
				"date": this.date
			})
			.post()
			.success(function (response) {
				this.timeFromDate = response.data.time;

				if (this.timeFromDate > 0) {
					this.dateTimeFromDate = new Date(this.timeFromDate * 1000).format("Y-m-d H:i:s");
				}
				else {
					this.dateTimeFromDate = "-";
				}
			});
	};

	this.setNow = function () {
		now = new Date();
		this.timestamp = Math.floor(now.getTime()/1000);
		this.timeToDate();
	};
});