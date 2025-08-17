function runQuillEditor(){
  const container = document.querySelector('#editor-container');
  if (!container) return;
  var quill = new Quill('#editor-container', {
    theme: 'snow',
    modules: {
      toolbar: {
        container: [
          [{ 'font': [] }],
          [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'align': [] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'color': [] }, { 'background': [] }],
          ['link', 'video', 'image', { 'code-block': true }],
        ],
        handlers: {
          bold: function(value) { applyFormat.call(this, 'bold', value); },
          italic: function(value) { applyFormat.call(this, 'italic', value); },
          underline: function(value) { applyFormat.call(this, 'underline', value); },
          strike: function(value) { applyFormat.call(this, 'strike', value); }
        }
      }
    }
  });

  function applyFormat(format, value) {
    this.quill.format(format, value);
    setTimeout(() => this.quill.focus(), 0);
  }

  const content = document.querySelector('input[name=description]');
  document.querySelector('form').addEventListener('submit', function(e) {
    content.value = quill.root.innerHTML;
  });

  document.querySelectorAll('.ql-toolbar button, .ql-toolbar span').forEach(el => {
    el.addEventListener('mousedown', e => e.preventDefault());
  });

  document.querySelector('#quillDeleteButton').addEventListener('click', () => {
    quill.setText('');
    content.value = '';
  });
}

export {
  runQuillEditor,
};