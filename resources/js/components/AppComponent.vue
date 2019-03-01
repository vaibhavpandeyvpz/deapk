<template>
    <main class="container">
        <div class="row">
            <div class="col-md-4 col-lg-3">
                <upload-component @queued="onApkQueued"></upload-component>
            </div>
            <div class="col-md-8 col-lg-9">
                <browser-component :apk="apk" class="mb-3" ref="browser" v-if="status === 1"></browser-component>
                <div class="card border-dashed mb-3" v-else-if="status === -1">
                    <div class="card-body text-center px-3 py-5">
                        <i class="fas fa-minus-circle fa-4x text-danger mb-3"></i>
                        <h5 class="card-title">Failed</h5>
                        <p class="card-text">Could not decompile {{ apk.name }}.</p>
                    </div>
                </div>
                <div class="card border-dashed mb-3" v-else-if="status === 0">
                    <div class="card-body text-center px-3 py-5">
                        <i class="fas fa-circle-notch fa-4x fa-spin text-warning mb-3"></i>
                        <h5 class="card-title">Decompiling&hellip;</h5>
                        <p class="card-text">Please wait while your file is processing.</p>
                        <p class="card-text">
                            <small>
                                <strong>Name:</strong> {{ apk.name }}<br>
                                <strong>ID:</strong> {{ apk.id }}
                            </small>
                        </p>
                    </div>
                </div>
                <div class="card border-dashed mb-3" v-else>
                    <div class="card-body text-center px-3 py-5">
                        <i class="fas fa-arrow-circle-left fa-4x text-primary mb-3 d-none d-md-block"></i>
                        <i class="fas fa-arrow-circle-up fa-4x text-primary mb-3 d-md-none"></i>
                        <h5 class="card-title">Upload</h5>
                        <p class="card-text">Please select a file to upload and decompile.</p>
                    </div>
                </div>
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-question-circle"></i> About</h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            Created &amp; maintained by
                            <a href="https://vaibhavpandey.com/" target="_blank">Vaibhav Pandey -aka- VPZ</a>.
                        </h6>
                        <p class="card-text">
                            <strong>DeAPK</strong> is an
                            <a href="https://github.com/vaibhavpandeyvpz/deapk" target="_blank">open-source</a>,
                            online APK decompiler which lets you upload an APK and then decompile it to
                            <strong>Smali</strong> or <strong>Java</strong> sources. It is built using
                            <a href="https://github.com/vaibhavpandeyvpz/deapk" target="_blank">Laravel</a>,
                            <a href="https://vuejs.org/" target="_blank">Vue.js</a>,
                            <a href="https://getbootstrap.com/" target="_blank">Bootstrap</a>,
                            <a href="https://fontawesome.com/" target="_blank">FontAwesome</a>,
                            <a href="https://pusher.com/" target="_blank">Pusher</a>,
                            <a href="https://redis.io/" target="_blank">Redis</a>,
                            <a href="https://www.mysql.com/" target="_blank">MySQL</a>,
                            <a href="https://ibotpeaches.github.io/Apktool/" target="_blank">apktool</a>,
                            <a href="https://github.com/skylot/jadx" target="_blank">jadx</a>
                            and hosted atop
                            <a href="https://www.digitalocean.com" target="_blank">DigitalOcean</a>&apos;s cloud
                            platform.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
    .border-dashed {
        border-style: dashed;
    }
</style>

<script>
    export default {
        data() {
            return {
                apk: {
                    id: null,
                    name: null
                },
                status: null
            }
        },
        methods: {
            onApkQueued(data) {
                if (this.apk.id) {
                    Echo.leaveChannel(this.apk.id)
                }
                this.apk = data;
                this.status = 0;
                Echo.channel(data.id)
                    .listen('ArchiveCreated', (e) => {
                        if (window.open(e.url) === null) {
                            location.href = e.url
                        }
                        this.$refs.browser.onArchiveDownloaded()
                    })
                    .listen('DecompileFinished', (e) => this.status = e.status ? 1 : -1)
            }
        }
    }
</script>
