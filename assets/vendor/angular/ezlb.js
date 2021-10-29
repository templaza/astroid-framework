"use strict";

function _possibleConstructorReturn(self, call) {
   if (!self) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
   }
   return call && (typeof call === "object" || typeof call === "function") ? call : self;
}

function _inherits(subClass, superClass) {
   if (typeof superClass !== "function" && superClass !== null) {
      throw new TypeError("Super expression must either be null or a function, not " + typeof superClass);
   }
   subClass.prototype = Object.create(superClass && superClass.prototype, {
      constructor: {
         value: subClass,
         enumerable: false,
         writable: true,
         configurable: true
      }
   });
   if (superClass)
      Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass;
}

function _classCallCheck(instance, Constructor) {
   if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
   }
}

String.prototype.shuffle = function () {
   var a = this.split(""),
      n = a.length;

   for (var i = n - 1; i > 0; i--) {
      var j = Math.floor(Math.random() * (i + 1));
      var tmp = a[i];
      a[i] = a[j];
      a[j] = tmp;
   }
   return a.join("");
};

function generateID() {
   return Math.random().toString(36).substr(2, 9);
   var r = Math.random() >= 0.5;
   if (r) {
      var x = Math.floor((Math.random() * 10) + 1);
      var y = Math.floor((Math.random() * 100) + 1);
      var z = Math.floor((Math.random() * 10) + 1);
   } else {
      var x = Math.floor((Math.random() * 10) + 1);
      var y = Math.floor((Math.random() * 10) + 1);
      var z = Math.floor((Math.random() * 100) + 1);
   }
   var t = Date.now();
   return x + y + z + t.toString();
}

function refreshID(_object, _type) {
   switch (_type) {
      case 'layout':
         _object.sections.forEach(function (_section) {
            refreshID(_section, 'section');
         });
         break;
      case 'section':
         _object.id = generateID();
         _object.rows.forEach(function (_row) {
            refreshID(_row, 'row');
         });
         break;
      case 'row':
         _object.id = generateID();
         _object.cols.forEach(function (_col) {
            refreshID(_col, 'col');
         });
         break;
      case 'col':
         _object.id = generateID();
         _object.elements.forEach(function (_element) {
            refreshID(_element, 'element');
         });
         break;
      case 'element':
         _object.id = generateID();
         break;
   }
}

var AstroidElement = function AstroidElement(type, title) {
   _classCallCheck(this, AstroidElement);

   this.id = generateID();
   this.type = type;
   this.params = [{
      name: 'title',
      value: title
   }];
   this.refreshID = function () {
      this.id = generateID();
   };
};

var AstroidSection = function AstroidSection(type, title) {
   _classCallCheck(this, AstroidSection);

   this.id = generateID();
   this.type = type;
   this.rows = [];
   this.params = [{
      name: 'title',
      value: title
   }];
   this.refreshID = function () {
      this.id = generateID();
   };
};

var AstroidRegularSection = function (_AstroidSection) {
   _inherits(AstroidRegularSection, _AstroidSection);

   function AstroidRegularSection() {
      _classCallCheck(this, AstroidRegularSection);

      return _possibleConstructorReturn(this, (AstroidRegularSection.__proto__ || Object.getPrototypeOf(AstroidRegularSection)).call(this, 'regular-section', 'Astroid Section'));
   }

   return AstroidRegularSection;
}(AstroidSection);

var AstroidFullWidthSection = function (_AstroidSection2) {
   _inherits(AstroidFullWidthSection, _AstroidSection2);

   function AstroidFullWidthSection() {
      _classCallCheck(this, AstroidFullWidthSection);

      return _possibleConstructorReturn(this, (AstroidFullWidthSection.__proto__ || Object.getPrototypeOf(AstroidFullWidthSection)).call(this, 'ful-width', 'Full Width'));
   }

   return AstroidFullWidthSection;
}(AstroidSection);

var AstroidSpecialSection = function (_AstroidSection3) {
   _inherits(AstroidSpecialSection, _AstroidSection3);

   function AstroidSpecialSection() {
      _classCallCheck(this, AstroidSpecialSection);

      return _possibleConstructorReturn(this, (AstroidSpecialSection.__proto__ || Object.getPrototypeOf(AstroidSpecialSection)).call(this, 'special', 'Special Section'));
   }

   return AstroidSpecialSection;
}(AstroidSection);

var AstroidRow = function AstroidRow() {
   _classCallCheck(this, AstroidRow);

   this.id = generateID();
   this.cols = [];
   this.type = "row";
};

var AstroidColumn = function AstroidColumn() {
   _classCallCheck(this, AstroidColumn);

   this.id = generateID();
   this.elements = [];
   this.size = 12;
   this.type = "column";
   this.params = [{
      name: 'title',
      value: ''
   }];
};

astroidFramework.controller('layoutController', function ($scope, $compile) {

   // Global Variables
   // All Types of Grid
   $scope.grids = ASTROID_GRIDS;

   // All types of Elements
   $scope.elements = AstroidLayoutBuilderElements;

   // All type of Sections
   $scope.sections = [];
   $scope.sections.push(AstroidRegularSection);
   //$scope.sections.push(AstroidFullWidthSection);
   //$scope.sections.push(AstroidSpecialSection);

   // Default Page Options
   $scope.chooseRow = {
      open: 0,
      section: null
   };
   $scope.chooseRowColumns = {
      open: 0,
      row: null,
      section: null
   };
   $scope.chooseElement = {
      open: 0,
      column: null,
      row: null,
      section: null
   };
   $scope.chooseSection = {
      open: 0,
      section: null
   };
   $scope.savingSection = {
      open: 0,
      section: null
   };
   $scope.editingElement = {
      open: 0,
      element: null,
      template: ''
   };
   $scope.editSectionTitle = {
      'title': '',
      editing: false
   };
   // Default Variables
   $scope.layout = _layout;

   $scope.save = function () {
      // console.log($scope.layout);
   };

   $scope.focusEditTitle = function ($event, _section) {

      $scope.editSectionTitle.editing = true;
      $scope.editSectionTitle.title = $scope.getParam(_section, 'title');

      setTimeout(function () {
         $($event.currentTarget).siblings('input').focus();
      }, 50);
   };

   $scope.layoutHistory = [];
   $scope.historyIndex = -1;
   $scope.backIndex = -1;
   // Global Functions

   $scope.getObject = function (_class) {
      return new _class();
   };

   // Section Functions

   $scope.addingSection = function (_sectionIndex) {
      $scope.chooseSection = {
         open: 1,
         section: _sectionIndex
      };
   };

   $scope.addSection = function (_index) {
      var _sections = $scope.layout.sections;
      var _section = new AstroidSection('section', 'Astroid Section');
      var _col = new AstroidColumn();
      _col.size = 12;

      var _row = new AstroidRow();
      _row.cols.push(_col);

      _section.rows.push(_row);

      var sectionIndex = 0;
      if (_index == null) {
         _sections.push(_section);
         sectionIndex = _sections.length - 1;
      } else {
         _sections.splice(_index + 1, 0, _section);
         sectionIndex = _index + 1;
      }
      $scope.layout.sections = _sections;
      $scope.chooseSection = {
         open: 0,
         section: null
      };

      $scope.editRow(0, sectionIndex);

      $scope.actionPreformed();
   };

   $scope.duplicateSection = function (_sectionIndex) {
      var _section = angular.copy($scope.layout.sections[_sectionIndex]);
      _section = $scope.checkBeforeDuplicate('section', _section);
      refreshID(_section, 'section');
      var _sections = $scope.layout.sections;
      _sections.splice(_sectionIndex + 1, 0, _section);
      $scope.layout.sections = _sections;
      $scope.actionPreformed();
   };

   $scope.addToLibrarySection = function (_section) {
      // console.log(_section);
      // console.log("Added to library");
      $scope.savingSection = {
         open: 0,
         section: null
      };
   };

   $scope.removeSection = function (_sectionIndex) {
      var c = confirm('Are you sure?');
      if (c) {
         var _sections = $scope.layout.sections;
         _sections.splice(_sectionIndex, 1);
         $scope.layout.sections = _sections;
         $scope.actionPreformed();
      }
   };

   $scope.updateSectionTitle = function (_section) {
      if ($scope.editSectionTitle.title == '') {
         $scope.editSectionTitle.title = 'Astroid Section';
      }
      $scope.setParam(_section, 'title', $scope.editSectionTitle.title);
      $scope.editSectionTitle.editing = false;
      $scope.editSectionTitle.title = '';
   };

   // Row Functions
   $scope.editRow = function (_rowIndex, _sectionIndex) {
      $scope.chooseRowColumns = {
         open: 1,
         row: _rowIndex,
         section: _sectionIndex
      };
   };

   $scope.updateRow = function (_rowIndex, _sectionIndex, _grid) {

      if (_grid == 'custom') {
         var _grid = $scope.getCustomGrid();
         if (_grid === false) {
            return false;
         }
      }

      var _columns = $scope.layout.sections[_sectionIndex].rows[_rowIndex].cols;
      var _updatedColumns = [];

      if (_grid.length < _columns.length) {
         // decresing columns
         _columns.forEach(function (_column, _i) {
            if (typeof _grid[_i] != 'undefined') {
               _column.size = _grid[_i];
               _updatedColumns.push(_column);
            } else {
               var _elements = _updatedColumns[_grid.length - 1].elements;
               _column.elements.forEach(function (_el) {
                  _elements.push(_el);
               });
               _updatedColumns[_grid.length - 1].elements = _elements;
            }
         });
      } else {
         // incresing or same columns
         _grid.forEach(function (_size, _i) {
            if (typeof _columns[_i] != 'undefined') {
               var _c = _columns[_i];
               _c.size = _size;
               _updatedColumns.push(_c);
            } else {
               var _col = new AstroidColumn();
               _col.size = _size;
               _updatedColumns.push(_col);
            }
         });
      }

      $scope.layout.sections[_sectionIndex].rows[_rowIndex].cols = _updatedColumns;
      $scope.chooseRowColumns = {
         open: 0,
         row: null,
         section: null
      };
      $scope.actionPreformed();
   };

   $scope.getCustomGrid = function () {
      var grid = prompt("Please enter custom grid size (eg. 2+3+6+1)", "");
      if (grid != null) {
         grid = grid.split('+');
         if ($scope.isValidGrid(grid)) {
            return grid;
         } else {
            alert("Invalid grid size.");
            return false;
         }
      }
      return false;
   };

   $scope.isValidGrid = function (_grid) {
      var _total = 0;
      _grid.forEach(function (_g) {
         _total += parseInt(_g);
      });
      if (_total == 12) {
         return true;
      } else {
         return false;
      }
   };

   $scope.duplicateRow = function (_rowIndex, _sectionIndex) {
      var _row = angular.copy($scope.layout.sections[_sectionIndex].rows[_rowIndex]);

      var _row = $scope.checkBeforeDuplicate('row', _row);

      refreshID(_row, 'row');
      var _rows = $scope.layout.sections[_sectionIndex].rows;
      _rows.splice(_rowIndex + 1, 0, _row);
      $scope.layout.sections[_sectionIndex].rows = _rows;
      $scope.actionPreformed();
   };

   $scope.addingRow = function (_index) {
      $scope.chooseRow = {
         open: 1,
         section: _index
      };
   };

   $scope.addRow = function (_index, _layout) {

      if (_layout == 'custom') {
         var _layout = $scope.getCustomGrid();
         if (_layout === false) {
            return false;
         }
      }

      var _section = $scope.layout.sections[_index];
      var _row = new AstroidRow();
      _layout.forEach(function (_size) {
         var _col = new AstroidColumn();
         _col.size = _size;
         _row.cols.push(_col);
      });
      _section.rows.push(_row);
      $scope.layout.sections[_index] = _section;

      $scope.chooseRow.open = 0;
      $scope.chooseRow.section = null;
      $scope.actionPreformed();
   };

   $scope.removeRow = function (_rowIndex, _sectionIndex) {
      var c = confirm('Are you sure?');
      if (c) {
         var _rows = $scope.layout.sections[_sectionIndex].rows;
         _rows.splice(_rowIndex, 1);
         $scope.layout.sections[_sectionIndex].rows = _rows;
         $scope.actionPreformed();
      }
   };

   // Element Functions
   $scope.setColumn = function (_column) {
      if (typeof _column.type == 'undefined') {
         _column.type = 'column';
      }
   };

   $scope.addingElement = function (_colIndex, _rowIndex, _sectionIndex, _elementIndex) {
      $scope.chooseElement = {
         open: 1,
         column: _colIndex,
         row: _rowIndex,
         section: _sectionIndex,
         element: _elementIndex
      };
   };

   $scope.addElement = function (_colIndex, _rowIndex, _sectionIndex, _elementIndex, _element) {
      _element = new AstroidElement(_element.type, _element.title);
      var _elements = $scope.layout.sections[_sectionIndex].rows[_rowIndex].cols[_colIndex].elements;
      if (_elementIndex == null) {
         _elements.push(_element);
      } else {
         _elements.splice(_elementIndex + 1, 0, _element);
      }
      $scope.layout.sections[_sectionIndex].rows[_rowIndex].cols[_colIndex].elements = _elements;
      $scope.chooseElement = {
         open: 0,
         column: null,
         row: null,
         section: null,
         element: null
      };
      $scope.actionPreformed();
      $scope.editElement(_element);
   };

   $scope.removeElement = function (_elementIndex, _colIndex, _rowIndex, _sectionIndex) {
      var c = confirm('Are you sure?');
      if (c) {
         var _elements = $scope.layout.sections[_sectionIndex].rows[_rowIndex].cols[_colIndex].elements;
         _elements.splice(_elementIndex, 1);
         $scope.layout.sections[_sectionIndex].rows[_rowIndex].cols[_colIndex].elements = _elements;
         $scope.actionPreformed();
      }
   };

   $scope.getParam = function (_element, _key) {
      var _value = '';
      _element.params.forEach(function (_param) {
         if (_param.name == _key) {
            _value = _param.value;
            return false;
         }
      });

      if (_element.type == 'module_position' && _value == '') {
         _value = $scope.getParam(_element, 'position');
      }

      return _value;
   };

   $scope.setParam = function (_element, _key, _value) {
      _element.params.forEach(function (_param) {
         if (_param.name == _key) {
            _param.value = _value;
            return false;
         }
      });
      return true;
   };

   $scope.elementParams = {};

   $scope.editElement = function (_element, _focus) {
      Admin.ringLoading($('#element-settings').children('.ezlb-pop-body'), true);
      var _template = $('#element-form-template-' + _element.type).html();
      $scope.elementParams = {};
      var _temp = {};
      angular.element(document.getElementById('element-settings-form')).html($compile(_template)($scope, function () {
         $('#element-settings').addClass('open');
         var _params = {};
         //console.log(_element.params);
         if (typeof _element.params != 'undefined') {
            _element.params.forEach(function (_p) {
               _params[_p.name] = _p.value;
            });
         }
         _temp = angular.copy(_params);
         $scope.elementParams = _params;
         setTimeout(function () {
            var _change = false;
            $.each(_temp, function (_key, _value) {
               var _f = $('#element-settings-form').find('[name="' + _key + '"]');
               if (_f.length) {
                  if (_f.attr('type') == 'radio') {
                     //console.log(_value);
                     $scope.elementParams[_key] = _value;
                     _change = true;
                  }
               }
            });
            if (_change) {
               $scope.$apply();
            }


            Admin.ringLoading($('#element-settings').children('.ezlb-pop-body'), false);
            if (typeof _focus != 'undefined') {
               $('#element-settings').find('#' + _focus).focus();
            }
            Admin.initPop();
         }, 500);
      }));

      $('#element-form-' + _element.type).submit(function (event) {
         event.preventDefault(event);
         var _data = $(this).serializeArray();
         _element.params = _data;
         //console.log(_element.params);
         $scope.elementParams = {};
         $('#element-settings-save').unbind();
         $('#element-form-' + _element.type).unbind();
         angular.element(document.getElementById('element-settings-form')).html($compile('')($scope));
         $('#element-settings').removeClass('open');
         $scope.$apply();
         return false;
      });
      $('#element-settings-save').bind('click', function () {
         $('#element-form-' + _element.type).submit();
      });
      $('#element-settings-close').click(function () {
         $('#element-settings-save').unbind();
         $('#element-form-' + _element.type).unbind();
         $scope.elementParams = {};
         angular.element(document.getElementById('element-settings-form')).html($compile('')($scope));
         $scope.$apply();
         $('#element-settings').removeClass('open');
      });
   };

   $scope.saveElement = function (_form) {
      var _data = $('#' + _form).serialize();
      //console.log(_data);
   };

   $scope.getElementByType = function (_type) {
      var _element = {};
      $scope.elements.forEach(function (_el) {
         if (_el.type == _type) {
            _element = _el;
         }
      });
      return _element;
   };

   $scope.duplicateElement = function (_elementIndex, _colIndex, _rowIndex, _sectionIndex) {
      var _element = angular.copy($scope.layout.sections[_sectionIndex].rows[_rowIndex].cols[_colIndex].elements[_elementIndex]);
      refreshID(_element, 'element');
      var _elements = $scope.layout.sections[_sectionIndex].rows[_rowIndex].cols[_colIndex].elements;
      _elements.splice(_elementIndex + 1, 0, _element);
      $scope.layout.sections[_sectionIndex].rows[_rowIndex].cols[_colIndex].elements = _elements;
      $scope.actionPreformed();
   };

   $scope.checkBeforeDuplicate = function (_type, _object) {
      switch (_type) {
         case 'section':
            _object.rows.forEach(function (_row) {
               _row = $scope.checkBeforeDuplicate('row', _row);
            });
            return _object;
            break;
         case 'row':
            _object.cols.forEach(function (_col) {
               _col = $scope.checkBeforeDuplicate('column', _col);
            });
            return _object;
            break;
         case 'column':
            var _elements = [];
            _object.elements.forEach(function (_element) {
               if ($scope.checkBeforeDuplicate('element', _element)) {
                  _elements.push(_element);
               }
            });
            _object.elements = _elements;
            return _object;
            break;
         case 'element':
            return $scope.canAddElement($scope.getElementByType(_object.type));
            break;
      }
   };

   // History functions
   $scope.actionPreformed = function () {
      $scope.saveHistory();
      setTimeout(function () {
         Admin.refreshScroll();
      }, 200);
   };

   $scope.saveHistory = function () {
      var _history = $scope.layoutHistory;
      _history.push({
         date: new Date(),
         layout: angular.copy($scope.layout)
      });
      $scope.layoutHistory = _history;
      $scope.historyIndex = $scope.historyIndex + 1;
      $scope.backIndex = $scope.backIndex + 1;
   };

   $scope.saveHistory();

   $scope.setFormData = function (form, data) {
      $.each(data, function () {
         var key = this.name;
         var value = this.value;
         var ctrl = $('[name="' + key + '"]', form);
         switch (ctrl.prop("type")) {
            case "radio":
            case "checkbox":
               ctrl.each(function () {
                  if ($(this).attr('value') == value)
                     $(this).attr("checked", value);
               });
               break;
            default:
               ctrl.val(value);
         }
         ctrl.change();
      });
   };

   $scope.canAddElement = function (_element) {
      if (_element.multiple) {
         return true;
      } else {
         if ($scope.hasElement(_element.type)) {
            return false;
         } else {
            return true;
         }
      }
   };

   $scope.hasElement = function (_type) {
      var _has = false;
      $scope.layout.sections.forEach(function (_section) {
         if (_has) {
            return false;
         }
         _section.rows.forEach(function (_row) {
            if (_has) {
               return false;
            }
            _row.cols.forEach(function (_col) {
               if (_has) {
                  return false;
               }
               _col.elements.forEach(function (_element) {
                  if (_has) {
                     return false;
                  }
                  if (_element.type == _type) {
                     _has = true;
                     return false;
                  }
               });
            });
         });
      });
      return _has;
   };

   $scope.back = function () {
      //      if ($scope.backIndex < 0) {
      //         console.log("No more history");
      //         return;
      //      }
      //      $scope.layout = angular.copy($scope.layoutHistory[$scope.backIndex].layout);
      //      $scope.backIndex = $scope.backIndex - 1;
   };

   $scope.forward = function () {
      //                           if (typeof $scope.layoutHistory[$scope.historyIndex + 1] == 'undefined') {
      //                              return;
      //                           }
      //                           $scope.layout = $scope.layoutHistory[$scope.historyIndex + 1].layout;
   };

   $scope.exportLayout = function () {
      var _layout = angular.copy($scope.layout);
      var dataStr = JSON.stringify(_layout);
      var dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);
      var exportFileDefaultName = 'astroid-layout.json';
      var linkElement = document.createElement('a');
      linkElement.setAttribute('href', dataUri);
      linkElement.setAttribute('download', exportFileDefaultName);
      linkElement.click();
   };

   $scope.importLayout = function () {
      $('#astroid-layout-import').click();
      //      var _layout = angular.copy($scope.layout);
      //      let dataStr = JSON.stringify(_layout);
      //      let dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);
      //      let exportFileDefaultName = 'astroid-layout.json';
      //      let linkElement = document.createElement('a');
      //      linkElement.setAttribute('href', dataUri);
      //      linkElement.setAttribute('download', exportFileDefaultName);
      //      linkElement.click();
   };
});
(function ($) {
   $(function () {
      $('.compress').click(function () {
         $(this).parent('.ezlb-pop-header').parent('.ezlb-pop-body').addClass('left-push');
      });
      $('.expand').click(function () {
         $(this).parent('.ezlb-pop-header').parent('.ezlb-pop-body').removeClass('left-push');
      });

      $(document).bind('keydown', 'ctrl+z', function () {
         angular.element(document.getElementById('layoutController')).scope().back();
      });
      $(document).bind('keydown', 'meta+z', function () {
         angular.element(document.getElementById('layoutController')).scope().back();
      });
      $(document).bind('keydown', 'ctrl+y', function () {
         angular.element(document.getElementById('layoutController')).scope().forward();
      });
      $(document).bind('keydown', 'meta+y', function () {
         angular.element(document.getElementById('layoutController')).scope().forward();
      });
   });
})(jQuery);

function uploadLayoutJSON() {
   var input = document.getElementById('astroid-layout-import');
   if (!input) {} else if (!input.files) {} else if (!input.files[0]) {} else {
      var file = input.files[0];
      var reader = new FileReader();
      reader.addEventListener("load", function () {
         var _json = checkUploadedJSON(reader.result);
         if (_json !== false) {
            var scope = angular.element(document.getElementById("layoutController")).scope();
            scope.layout = _json;
            scope.$apply();
         }
      }, false);
      if (file) {
         reader.readAsText(file);
      }
   }
   $("#astroid-layout-import").val("");
}

function checkUploadedJSON(text) {
   if (/^[\],:{}\s]*$/.test(text.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
      var json = JSON.parse(text);
   } else {
      Admin.notify('Invalid JSON');
      return false;
   }

   if (json == '' || json == null || typeof json.sections == 'undefined') {
      Admin.notify('Invalid Layout file');
      return false;
   }
   return json;
}