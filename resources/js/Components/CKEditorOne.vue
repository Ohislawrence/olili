<template>
  <div class="ckeditor">
    <div v-if="showToolbar" class="ckeditor__toolbar"></div>
    <ckeditor
      :editor="editor"
      v-model="editorData"
      :config="editorConfig"
      @ready="onReady"
      @blur="onBlur"
      @input="onInput"
    ></ckeditor>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { CKEditor } from '@ckeditor/ckeditor5-vue';
import DecoupledEditor from '@ckeditor/ckeditor5-build-decoupled-document';

const props = defineProps({
  modelValue: String,
  placeholder: String,
  showToolbar: {
    type: Boolean,
    default: true
  },
  showCharacterCount: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['update:modelValue', 'blur']);

const editorData = ref(props.modelValue || '');
const editorInstance = ref(null);

const editor = DecoupledEditor;

const editorConfig = ref({
  placeholder: props.placeholder,
  simpleUpload: {
    uploadUrl: route('admin.upload-image'),
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    }
  }
});

const onReady = (editor) => {
  editorInstance.value = editor;

  // Insert the toolbar for decoupled editor
  if (props.showToolbar) {
    const toolbarContainer = document.querySelector('.ckeditor__toolbar');
    toolbarContainer.appendChild(editor.ui.view.toolbar.element);
  }

  editor.setData(props.modelValue || '');
};

const onInput = () => {
  emit('update:modelValue', editorData.value);
};

const onBlur = (event, editor) => {
  emit('blur', editor.getData());
};

watch(() => props.modelValue, (newValue) => {
  if (newValue !== editorData.value && editorInstance.value) {
    editorData.value = newValue;
    editorInstance.value.setData(newValue || '');
  }
});

onMounted(() => {
  editorData.value = props.modelValue || '';
});
</script>

<style>
.ckeditor .ck.ck-editor {
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
}

.ckeditor .ck.ck-editor__editable:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
  outline: none;
}

.ckeditor .ck.ck-toolbar {
  border: none;
  border-bottom: 1px solid #d1d5db;
  background: #f9fafb;
  border-radius: 0.375rem 0.375rem 0 0;
}

.ckeditor .ck.ck-editor__editable {
  min-height: 200px;
  padding: 1rem;
  border: none;
}
</style>
