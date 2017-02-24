// -----------------------------------------------------------
// MEDIUM
// -----------------------------------------------------------
$(function() {
    rangy.init();

    $('.medium-editable').each(function(index, item) {
        item = $(item);
        if (!item.data('Medium')) {
            item.data('Medium', new Medium(item));
        }
    });
});

var Medium = function(element) {
    this.element = element;
    this.required = this.element.hasClass('js-required');
    
    this.editorOptions = mediumAttributeOptions();
    this.editor = new MediumEditor(this.element, this.editorOptions);
    this.editor.subscribe('blur', this.blur.bind(this));
    this.element.data('MediumEditor', this.editor);

    this.elements = $(this.editor.elements);
    this.elements.data('MediumEditor', this.editor);

    this.insertOptions = mediumInsertOptions();
    this.insertOptions.editor = this.editor;
    this.element.mediumInsert(this.insertOptions);

    this.blur();
};

Medium.prototype.blur = function(e) {
    this.elements.find('.medium-insert-buttons, .medium-insert-active').remove();
    this.elements.find('p,h2,h3,ul,ol,a,strong,b,i,em').filter(':empty').remove();

    setTimeout(function() {
        this.editor.checkContentChanged(this.elements.get(0));

        if (!this.elements.children().length) {
            this.elements.addClass('medium-editor-placeholder');
        }

        if (this.required) {
            Required.change(e);
        }
    }.bind(this), 100);
};

Medium.prototype.validate = function() {
    return !!this.elements.text().length;
};

var MediumButton = MediumEditor.extensions.button.extend({
    'init': function () {
        this.contentFA = this.label;
        this.tagNames = [this.tag];
        var classes = this.class ? this.class.split(' ') : [];
        var name = classes.length ? classes.shift() : this.tag;

        MediumEditor.extensions.button.prototype.init.call(this);

        this.classApplier = rangy.createClassApplier(name, {
            'elementTagName': this.tag,
            'elementProperties': classes.length ? {
                'className': classes.join(' ')
            } : null,
            'normalize': true
        });
    },

    'handleClick': function () {
        this.classApplier.toggleSelection();
        if (this.classApplier.isAppliedToSelection()) {
            this.setActive();
        } else {
            this.setInactive();
        }
        this.base.checkContentChanged();
    },

    'isAlreadyApplied': function (node) {
        node = $(node);
        return (node.is(this.tag) && (!this.class || node.hasClass(this.class)));
    }
});