<template>
    <div class="card">
        <div class="card-header"><strong>Decompiled:</strong> {{ apk.name }}</div>
        <div class="d-flex m-1">
            <button class="btn btn-light" @click="home"><i class="fas fa-home"></i></button>
            <button class="btn btn-light ml-1" @click="back" v-if="mode === 'viewer'">
                <i class="fas fa-arrow-circle-left"></i>
            </button>
            <button class="btn btn-dark flex-shrink-0 ml-1" @click="archive" :disabled="archiving">
                <i class="fas fa-circle-notch fa-spin mr-1" v-if="archiving"></i>
                <i class="fas fa-file-archive text-warning mr-1" v-else></i>
                *.zip
            </button>
            <!--suppress HtmlFormInputWithoutLabel -->
            <input class="form-control flex-grow-1 ml-1" placeholder="current directory" :value="cwd">
        </div>
        <div class="card-body p-0" v-if="mode === 'viewer'">
            <div class="border mx-1 mb-1">
                <codemirror-component :options="code.options"
                                      @ready="setup"
                                      :value="code.source"></codemirror-component>
            </div>
        </div>
        <div class="table-responsive" v-else>
            <table :class="`table${files.length > 0 ? ' table-hover' : ''}`">
                <thead class="bg-light">
                <tr>
                    <th class="text-center"><i class="fa fa-circle-notch fa-spin" v-if="loading"></i></th>
                    <th class="w-100">Name</th>
                    <th>Size</th>
                </tr>
                </thead>
                <tbody>
                <template v-if="files.length > 0">
                    <tr>
                        <td class="text-center"><i class="fas fa-folder fa-fw text-warning"></i></td>
                        <td colspan="2"><a @click.prevent="up" href="">&hellip;</a></td>
                    </tr>
                    <tr v-for="file in files">
                        <td class="text-center">
                            <i class="fas fa-folder fa-fw text-warning" v-if="file.type === 'dir'"></i>
                            <i class="fas fa-file fa-fw" v-else></i>
                        </td>
                        <td><a @click.prevent="() => browse(file)" href="">{{ file.name }}</a></td>
                        <td>{{ file.size }}</td>
                    </tr>
                </template>
                <tr v-else>
                    <td class="text-center text-muted" colspan="3">No files here.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    .table {
        margin-bottom: 0 !important;
        th, td {
            white-space: nowrap;
        }
    }
</style>

<script>
    import filesize from 'filesize';
    import { codemirror as CodemirrorComponent } from 'vue-codemirror';
    import 'codemirror/addon/selection/active-line.js';
    import 'codemirror/mode/clike/clike.js';
    import 'codemirror/mode/properties/properties.js';
    import 'codemirror/mode/xml/xml.js';
    import 'codemirror/mode/yaml/yaml.js';
    import 'codemirror/lib/codemirror.css';

    const modes = {
        'java': 'text/x-java',
        'properties': 'text/x-properties',
        'txt': 'text/plain',
        'xml': 'application/xml',
        'yml': 'text/x-yaml',
    };

    export default {
        components: {
            CodemirrorComponent
        },
        computed: {
            cwd() {
                let path = '/';
                if (this.breadcrumbs.length > 0) {
                    path += this.breadcrumbs.join('/')
                }
                return path
            }
        },
        data() {
            return {
                archiving: false,
                breadcrumbs: [],
                code: {
                    options: {
                        autofocus: true,
                        lineNumbers: true,
                        mode: modes.txt,
                        readOnly: true,
                        styleActiveLine: true,
                    },
                    source: '',
                },
                loading: false,
                mode: 'browser',
                files: [],
            }
        },
        methods: {
            archive() {
                $.get('/api/archive?id=' + this.apk.id, () => this.archiving = true)
            },
            archived() {
                this.archiving = false
            },
            back() {
                this.$delete(this.breadcrumbs, this.breadcrumbs.length - 1);
                this.mode = 'browser'
            },
            browse(file) {
                this.breadcrumbs.push(file.name);
                if (file.type === 'dir') {
                    this.reload()
                } else {
                    const path = this.breadcrumbs.join('/');
                    const extn = file.name.split('.').pop();
                    if (typeof modes[extn] === 'undefined') {
                        const url = `/api/fetch?download=1&id=${this.apk.id}&path=${encodeURIComponent(path)}`;
                        if (window.open(url) === null) {
                            location.href = url
                        }
                    } else {
                        this.loading = true;
                        const jxhr = $.ajax({
                            data: { id: this.apk.id, path },
                            dataType: 'text',
                            success: contents => {
                                this.code.options.mode = modes[extn];
                                this.code.source = contents;
                                this.mode = 'viewer'
                            },
                            type: 'GET',
                            url: '/api/fetch'
                        });
                        jxhr.always(() => this.loading = false)
                    }
                }
            },
            goto(i) {
                const breadcrumbs = this.breadcrumbs.concat();
                breadcrumbs.splice(i + 1);
                this.breadcrumbs = breadcrumbs;
                this.reload()
            },
            home() {
                this.breadcrumbs = [];
                this.reload()
            },
            reload() {
                this.loading = true;
                const path = this.breadcrumbs.length > 0 ? this.breadcrumbs.join('/') : '/';
                $.get('/api/browse', { id: this.apk.id, path }, files => {
                    this.files = files.map(file => {
                        file.size = filesize(file.size);
                        return file
                    })
                }).always(() => this.loading = false)
            },
            setup(cm) {
                cm.setSize(null, 640)
            },
            up() {
                this.$delete(this.breadcrumbs, this.breadcrumbs.length - 1);
                this.reload()
            },
        },
        mounted() {
            this.reload()
        },
        props: ['apk'],
    }
</script>
