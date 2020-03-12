<template>
    <form class="mb-3" enctype="multipart/form-data" method="post" @submit.prevent="onFormSubmit">
        <div class="alert alert-warning text-justify" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            Due to server limitations, only *.apk files sizing upto 50 MBs are allowed.
        </div>
        <fieldset :disabled="submitting">
            <div class="form-group">
                <div class="custom-file">
                    <input :class="`custom-file-input${!!errors.apk ? ' is-invalid' : ''}`"
                           id="upload-apk"
                           ref="apk"
                           required
                           type="file"
                           @change="onFileSelect">
                    <label class="custom-file-label" for="upload-apk">{{ label }}</label>
                    <div class="invalid-feedback" v-if="!!errors.apk">{{ errors.apk[0] }}</div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input class="custom-control-input" id="upload-sources" type="checkbox" v-model="form.sources">
                    <label class="custom-control-label" for="upload-sources">Decompile sources?</label>
                </div>
            </div>
            <button class="btn btn-success">
                <i class="fas fa-circle-notch fa-spin mr-1" v-if="submitting"></i>
                <i class="fab fa-android mr-1" v-else></i>
                Upload<span v-if="submitting">&hellip;</span>
            </button>
        </fieldset>
    </form>
</template>

<style scoped>
    .custom-file-label {
        overflow: hidden;
    }
</style>

<script>
    export default {
        data() {
            return {
                errors: {},
                form: {
                    apk: null,
                    sources: true
                },
                label: 'Choose an *.apk',
                submitting: false
            }
        },
        methods: {
            onFileSelect() {
                this.form.apk = this.$refs.apk.files[0];
                this.label = !!this.form.apk ? this.form.apk.name : 'Choose an *.apk'
            },
            onFormSubmit() {
                this.errors = {};
                this.submitting = true;
                const data = new FormData();
                data.append('apk', this.form.apk);
                if (this.form.sources) {
                    data.append('sources', '1')
                }
                $.ajax({
                    contentType: false,
                    data,
                    processData: false,
                    type: 'POST',
                    url: '/api/decompile'
                })
                    .always(() => this.submitting = false)
                    .done(data => this.$emit('queued', data))
                    .fail(xhr => {
                        if (xhr.status === 422) {
                            this.errors = xhr.responseJSON.errors
                        } else {
                            this.errors = { apk: ['Unexpected server error has occurred.'] }
                        }
                    })
            }
        }
    }
</script>
