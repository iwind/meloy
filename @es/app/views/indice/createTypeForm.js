Tea.View.scope(function () {
	this.dataTypes = [];
	this.showFieldsBox = false;
	this.fields = [];

	Tea.action("@.field.types")
		.params({ "version": TEA.ACTION.data.serverVersion })
		.success(function (response) {
			this.dataTypes = response.data.groups;
		})
		.post();

	this.showFields = function () {
		this.showFieldsBox = !this.showFieldsBox;
	};

	this.addField = function (subType) {
		this.fields.push({
			"type": subType["code"]
		});
		this.showFieldsBox = false;
	}
});