(window.vcvWebpackJsonp4x=window.vcvWebpackJsonp4x||[]).push([[0],{"./node_modules/raw-loader/index.js!./portoHeaderMyaccount/cssMixins/myaccount.pcss":function(e,t){e.exports="#header.header-builder .porto-hb-myaccount-$selector {\r\n\t@if $fontSize != false {\r\n\t\tfont-size: $(fontSize);\r\n\t}\r\n\t@if $color != false {\r\n\t\tcolor: $(color);\r\n\t}\r\n}"},"./portoHeaderMyaccount/index.js":function(e,t,o){"use strict";o.r(t);var n=o("./node_modules/vc-cake/index.js"),s=o.n(n),c=o("./node_modules/@babel/runtime/helpers/extends.js"),r=o.n(c),a=o("./node_modules/@babel/runtime/helpers/classCallCheck.js"),i=o.n(a),l=o("./node_modules/@babel/runtime/helpers/createClass.js"),u=o.n(l),p=o("./node_modules/@babel/runtime/helpers/inherits.js"),d=o.n(p),m=o("./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"),f=o.n(m),y=o("./node_modules/@babel/runtime/helpers/getPrototypeOf.js"),b=o.n(y),v=o("./node_modules/react/index.js"),h=o.n(v);function x(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var o,n=b()(e);if(t){var s=b()(this).constructor;o=Reflect.construct(n,arguments,s)}else o=n.apply(this,arguments);return f()(this,o)}}var _=function(e){d()(o,e);var t=x(o);function o(e){return i()(this,o),t.call(this,e)}return u()(o,[{key:"render",value:function(){var e=this.props,t=e.id,o=e.editor,n=e.atts,s=this.applyDO("all"),c=n.icon_cl,a=n.el_class,i="my-account";a&&(i+=" "+a);var l=this.getMixinData("myaccount");l&&(i+=" porto-hb-myaccount-".concat(l.selector));var u=c||"porto-icon-user-2";return h.a.createElement("a",r()({className:i,href:porto_vc_vars.myaccount_url},o,{id:"el-"+t},s),h.a.createElement("i",{className:u}))}}]),o}(Object(n.getService)("portoComponent").shortcodeComponent);(0,s.a.getService("cook").add)(o("./portoHeaderMyaccount/settings.json"),(function(e){e.add(_)}),{css:!1,editorCss:!1,mixins:{myaccount:{mixin:o("./node_modules/raw-loader/index.js!./portoHeaderMyaccount/cssMixins/myaccount.pcss")}}})},"./portoHeaderMyaccount/settings.json":function(e){e.exports=JSON.parse('{"icon_cl":{"type":"string","access":"public","value":"","options":{"label":"Custom Icon Class"}},"font_size":{"type":"string","access":"public","value":"","options":{"label":"Font Size","cssMixin":{"mixin":"myaccount","property":"fontSize","namePattern":"[\\\\da-f]+"}}},"color":{"type":"color","access":"public","value":"","options":{"label":"Icon Color","cssMixin":{"mixin":"myaccount","property":"color","namePattern":"[\\\\da-f]+"}}},"el_class":{"type":"string","access":"public","value":"","options":{"label":"Extra class name","description":"Add an extra class name to the element and refer to it from Custom CSS option."}},"designOptions":{"type":"designOptions","access":"public","value":{},"options":{"label":"Design Options"}},"editFormTab1":{"type":"group","access":"protected","value":["icon_cl","font_size","color","el_class"],"options":{"label":"My Account Icon"}},"metaEditFormTabs":{"type":"group","access":"protected","value":["editFormTab1","designOptions"]},"relatedTo":{"type":"group","access":"protected","value":["General"]},"tag":{"access":"protected","type":"string","value":"portoHeaderMyaccount"}}')}},[["./portoHeaderMyaccount/index.js"]]]);