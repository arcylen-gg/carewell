<template>
    <span>
        <input type="file" :multiple="multiple" ref="upload" class="hidden" @change="uploadFile">
        <div class="file-holder">
            <div class="uploads-holder">
                <div class="file" v-for="(file, index) in files">
                    <span class="name"> 
                        <span class="type" v-if="file.image">
                            <img :src="file.source" alt="" />
                        </span>
                        <span class="type" v-else>{{ file.extension }}</span>
                        {{ file.name }} 
                    </span>
                    <span class="justify-end align-end text-right">
                        <span class="size">
                            {{ displayFileSizeInMb(file.size) }} MB
                        </span>
                        <v-icon class="remove" color="red" @click.native="removeFile(index)">mdi-close</v-icon>
                    </span>
                </div>
            </div>
            <div class="file uploader primary lighten-4 white--text select-files" @click="uploadNew">
                <span v-if="files.length">Selected Files  ({{ files.length }}) - ({{ displayFileSizeInMb(this.current_usage) }} Mb in total)</span>
                <span v-else>Please select atleast one file.</span>
                <span class="justify-end align-end text-right">
                    <v-icon color="primary" class="add">mdi-plus</v-icon>
                </span>
            </div>
        </div>

        <!-- <v-btn @click="uploadLive">UPLOAD TEST</v-btn> -->
    </span>
</template>

<script>
    export default {
        props: {
            eventName: {
                type: String,
                default: "setUploadData"
            },
            files: {
                type: Array,
                default: () => []
            },
            multiple : {
                type: Boolean,
                default: true
            },
            folder: {
                type: String,
                default: 'other'
            }
        },
        data() {
            return {
                current_usage:  0,
                upload_limit:   25000000
            }
        },
        methods : {
            uploadNew() {
                this.$refs.upload.click()
            },
            uploadFile(event) {
                let files               = Object.keys(event.target.files)
                let allowed_extensions  = ["PNG", "JPG", "JPEG", "PDF", "DOCX", "DOC", "CSV", "XLS", "TXT", "XLSX"]
                
                files.forEach((d, key) => {
                    let data            = event.target.files[key]
                    let index           = data.name.lastIndexOf(".")
                    let ext             = data.name.substr(index + 1, 100)
                    data.extension      = ext.toUpperCase()
                    data.is_uploaded    = 0
                    data.image          = 0

                    if(allowed_extensions.includes(data.extension))
                    {
                        this.current_usage += data.size

                        if(this.current_usage > this.upload_limit)
                        {
                            this.current_usage -= data.size
                            this.$refs.upload.value = ""
                            this.toaster(`You've reached the file upload limit. The limit per upload is ${this.upload_limit/1000000} Mb. Current usage is ${this.displayFileSizeInMb(this.current_usage)} Mb.`, 300)
                            return false
                        }

                        if(data.type.includes("image/"))
                        {
                            let reader = new FileReader();
                            let app = this
                            reader.onload = function(e){
                                data.source         = e.target.result
                                data.image          = 1
                                app.files.push(data)
                                EventBus.$emit('setUploadData', app.files)
                            }
                            reader.readAsDataURL(data);
                        }
                        else
                        {
                            this.files.push(data)   
                            EventBus.$emit('setUploadData', this.files)
                        }
                    }
                    else
                    {
                        this.toaster("This file is not supported!", 300)
                    }
                });

                this.$refs.upload.value = ""
            },
            removeFile(key) {
                this.current_usage -= this.files[key].size

                this.files.splice(key, 1)

                EventBus.$emit('setUploadData', this.files)
            },
            displayFileSizeInMb(size) {
                return (size/1000000).toFixed(3)
            },
            uploadLive() {
                const formData = new FormData();

                formData.append('folder', this.folder)

                for (var i = 0; i < this.files.length; i++) {
                    formData.append('files[]', this.files[i], this.files[i].name);
                    formData.append('extensions[]', this.files[i].extension);
                    formData.append('sizes[]', this.files[i].size);
                    formData.append('names[]', this.files[i].name);
                }

                axios
                .post(`/api/document`, formData)
                .then(response => {
                })
            }
        },
        mounted () {
        }
    }
</script>

<style lang="scss">
    

    .hidden {
        display: none;
    }

    .select-files {
        font-size: 14px;
        text-transform: uppercase;
    }

    .file-holder{
        width: 100%;
        .uploads-holder {
            max-height: 261px;
            overflow-y: auto;
        }
        .file{
            margin: 5px 0px;
            width: 100%;
            display: grid;
            grid-template-columns: 4fr 2fr;
            grid-gap: 10px;
            border: 1px solid #ced4da !important;
            padding: 5px 15px;

            .name {
                width:250px;
                text-overflow: ellipsis;
                word-break: break-all;
                overflow:hidden;
                white-space: nowrap;

                .type {
                    font-size: 12px;
                    font-weight: 600;
                    min-width: 50px;
                    max-width: 50px;
                    text-align: center;
                    background: #aaa;
                    display: inline-block;
                    padding: 3px;
                    color: #eee;
                    border: 0;
                    border-radius: 2px;

                    img {
                        width: 100%;
                    }
                }
            }

            .size {
                color: #777;
                font-size: 12px;
            }

            .remove {
                display: inline-block;
                cursor: pointer;
            }

            .add {
                cursor: pointer;
            }
        }

        .uploader{
                cursor: pointer;
            }
    }
</style>