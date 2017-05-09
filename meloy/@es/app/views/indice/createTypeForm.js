Tea.View.scope(function () {
	this.dataTypes = [];
	this.showFieldsBox = false;
	this.fields = [];

	this.load = function () {
		Tea.action("@.field.types")
			.params({ "version": this.serverVersion })
			.success(function (response) {
				this.dataTypes = response.data.groups;
			})
			.post();
	};
	this.load();

	this.showFields = function () {
		this.showFieldsBox = !this.showFieldsBox;
	};

	this.addField = function (subType) {
		this.fields.push({
			"type": subType["code"]
		});
		this.showFieldsBox = false;

		setTimeout(function () {
			var inputFields = document.querySelectorAll("#createForm input[name='fieldNames[]'][type='text']");
			if (inputFields.length > 0) {
				inputFields[inputFields.length - 1].focus();
			}
		}, 100);
	};

	this.removeField = function (index) {
		this.fields.$remove(index);
	};
});