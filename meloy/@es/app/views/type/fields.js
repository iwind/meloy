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

		var properties = this.mapping.properties;
		for (var fieldName in properties) {
			if (!properties.hasOwnProperty(fieldName)) {
				continue;
			}

			this.fields.push({
				"type": properties[fieldName].type,
				"name": fieldName,
				"canModify": false
			});
		}
	};

	this.load();

	this.showFields = function () {
		this.showFieldsBox = !this.showFieldsBox;
	};

	this.addField = function (subType) {
		this.fields.push({
			"type": subType["code"],
			"name": "",
			"canModify": true
		});
		this.showFieldsBox = false;

		setTimeout(function () {
			var inputFields = document.querySelectorAll("#updateForm input[name='fieldNames[]'][type='text']");
			if (inputFields.length > 0) {
				inputFields[inputFields.length - 1].focus();
			}
		}, 100);
	};

	this.removeField = function (index) {
		this.fields.$remove(index);
	};
});