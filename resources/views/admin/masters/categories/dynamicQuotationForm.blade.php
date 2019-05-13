<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary animated fadeInLeft">
				<div class="panel-heading">
					<h3 class="panel-title">Add Fields</h3>
				</div>
				<div class="panel-body" style="height:400px;overflow:auto;">
					<form class="form" ng-submit="saveField()">
						<div class="form-group">
							<label for="newField-name">Field Name:</label> <input type="text"
								class="form-control" id="newField-name" ng-model="newField.name" ng:required>
						</div>
						<div class="form-group">
							<label>Required: <input
								type="checkbox" id="newField-required"
								ng-model="newField.required">
							</label>
						</div>
						
						<div class="form-group">
							<label for="newField-order">Order Weight:</label> <input
								class="form-control" type="number" id="newField-order" ng-model="newField.order"
								value="1" ng-required  min="1">
						</div>
						<div class="form-group">
							<label for="newField-type">Field Type:</label> <select
								class="form-control" id="newField-type" ng-model="newField.type" ng-required>
								<option value="text" selected>Text</option>
								<option value="radio">Radio Buttons</option>
								<option value="select">Drop Menu (Select)</option>
<!--								<option value="multiple">Multi-Select</option>-->
<!--								<option value="checkbox">Toggle (Checkbox)</option>-->
								<option value="checkboxes">Checkboxes</option>
								<option value="textarea">Paragraph(s)</option>
								<option value="number">Number</option>
<!--								<option value="url">Url</option>-->
								<option value="phone">Phone</option>
								<option value="email">Email</option>
<!--								<option value="header">Heading</option>-->
							</select>
						</div>
						<ng-switch on="typeSwitch(newField.type)">
<!--						<div ng-switch-default class="form-group">
							<label for="newField-placeholder">Instructions:</label> <input
								class="form-control" type="text" id="newField-placeholder"
								ng-model="newField.placeholder">
						</div>-->
						<fieldset ng-switch-when="multiple">
							<legend>
								<a class="btn btn-primary btn-xs" ng-click="addOption()">Add Option</a>
							</legend>
							<div class="panel panel-primary"
								ng-repeat="option in newField.options|orderBy:'order'">
								<div class="panel-body">
									<b><i>Option:</i></b>
									<button class="btn btn-danger btn-xs"
										ng-click="splice(option, newField.options)">Remove</button>
									<div class="form-group">	
										Name: <input class="form-control" type="text" ng-model="option.name"
												ng-required>
										Value: <input class="form-control" type="text"
												ng-model="option.value">
										Order: <input class="form-control" type="number"
											ng-model="option.order" value="1"  min="1">
									</div>
								</div>
							</div>
						</fieldset>
						<span ng-switch-when="checkbox"></span> <span
							ng-switch-when="header"></span> </ng-switch>
						<div>
							<input class="btn btn-warning"type="submit" value="Create New Field">
						</div>
					</form>
				</div>
			</div>
		</div>
	
		<div class="col-md-6">
			<div class="panel panel-primary animated fadeInRight" >
				<div class="panel-heading">
					<h3 class="panel-title">Quotation Additional Fields Preview</h3>
				</div>
				<div class="panel-body" style="height:400px;overflow:auto;">
					<div ng-repeat="field in fields | orderBy:'order'">
						<ng-switch on="field.type">
						<h3 ng-switch-when="header" class="animated fadeInRight">
							{{field.order}} {{field.name}}
							<button class="btn btn-warning btn-xs" ng-click="editField(field)">Edit</button>
							<button class="btn btn-danger btn-xs" ng-click="splice(field, fields)">Remove</button>
						</h3>
						<div ng-switch-default class="input {{field.type}}"
							ng-class="field.required && 'required'">
							<div class="form-group">
								<label>
									{{field.order}} . {{field.name}} 
									<button class="btn btn-warning btn-xs" ng-click="editField(field)">Edit</button>
									<button class="btn btn-danger btn-xs" ng-click="splice(field, fields)">Remove</button>	
								</label>
							
								<ng-switch on="field.type"> 
									<input class="form-control animated fadeInRight" ng-switch-default
										type="{{field.type}}" ng-model="field.value"
										ng-bind-attr="{required:'{{field.required}}'}"
										value="{{field.value}}" placeholder="{{field.placeholder}}">
									<input ng-switch-when="checkbox" type="checkbox" class="animated fadeInRight"
										ng-model="field.value" value="{{field.value}}" id="field"
										placeholder="{{field.instructions}}"> 
									<textarea class="form-control animated fadeInRight"
										ng-switch-when="textarea" ng-model="field.value"
										placeholder="{{field.instructions}}">{{field.value}}
									</textarea> 
									<select class="form-control animated fadeInRight"
										ng-switch-when="select" ng-model="field.value">
										<option ng-repeat="option in field.options"
											value="{{option.value}}">{{option.name}}
										</option>
									</select> 
									<select class="form-control animated fadeInRight" 
										ng-switch-when="multiple" ng-model="field.value" multiple>
										<option ng-repeat="option in field.options"
											value="{{option.value}}">{{option.name}}
										</option>
									</select>
									<fieldset ng-switch-when="radio">
										<label ng-repeat="option in field.options"> 
											<input class="animated fadeInRight"
												type="radio" ng-model="field.value" value="{{option.value}}">
											{{option.name}}
										</label>
									</fieldset>
									<fieldset ng-switch-when="checkboxes">
										<label ng-repeat="option in field.options"> 
											<input class="animated fadeInRight"
												type="checkbox" ng-model="field.value[tokenize(option.name)]"
												value="{{option.value}}"> {{option.name}}
										</label>
									</fieldset>
								</ng-switch>
							</div>
						</div>
						</ng-switch>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="display:none;">
		<div class="col-md-12">
			<div class="panel panel-warning animated fadeInUp">
				<div class="panel-heading">
					<h3 class="panel-title">JSON Data</h3>
				</div>
				<div class="panel-body" style="height:100px;overflow:auto;">
					<pre>{{fields}}</pre>
                                        <textarea name="jsonData" id="jsonData" style="display:none;">{{fields}}</textarea>
				</div>
			</div>
		</div>
	</div>