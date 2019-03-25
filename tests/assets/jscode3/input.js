gettext("some message\nwith new line");

gettext('some message\nwith new line and\n"%1$s" two placeholders "%2$s"');


(function() {
    // var angular = require('angular');
    // var ngResource = require('angular-resource');
    // var sprintf = require('sprintf-js').sprintf;

    var app = angular.module('GettextTest', [ngResource, angularMoment]);

    app.factory('GettextTest', ['$resource', function($resource) {
        var constructor = $resource('/resource', { id: '@id' }, {
            'update': {
                method: 'PUT'
            }
        });

        constructor.prototype.issue_formatted = function() {
            if (!this.year || !this.issue) {
                return '';
            }

            return sprintf('%d / %\'02d', this.year, this.issue);
        };

        return constructor;
    }]);

    app.directive('mediaSelect', function() {
        return {
            restrict: 'A',
            require: '?^ngModel',
            replace: true,
            template: '<div class="media has-media">' +
                '<span class="image-wrapper">' +
                    '<span class="name"></span>' +
                '</span>' +
                '<span class="opts">' +
                    '<a href="#" class="iconized select">' +
                        '<i class="fa fa-image"></i> ' +
                        '<span>' + gettext('Select image ...') + '</span>' +
                    '</a>' +
                    '<a href="#" class="iconized remove">' +
                        '<i class="fa fa-times"></i> ' +
                        '<span>' + gettext('Delete image') + '</span>' +
                    '</a>' +
                '</span>' +
            '</div>',
            link: function() {
                //
            }
        }
    });
})();
