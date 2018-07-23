<template>
<div>
    <div id="myModal" class="modal" :class="{ fade: modalShown, in: modalShown }" tabindex="-1" role="dialog" style="display:none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Import data</h4>
                </div>
                <div class="modal-body">
                    <p>Copy your content in here</p>
                    <input type="hidden" id="groupId" name="groupId" v-model="group.id">
                    <textarea class="form-control" id="contentImport" rows="3" v-model="content"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" v-on:click="importData">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
</template>

<script>
    export default {
        props: ['group'],
        data() {
            return {
                content: '',
                modalShown: true
            }
        },

        methods: {
            importData(){
                axios.post('/group/'+this.group.id+'/import', {groupId: this.group.id, content: this.content})
                .then((response) => {
                    location.reload();
                    this.toggleModal();
                });
            },

            toggleModal() {
                this.modalShown = false;
                document.getElementById("myModal").style.display = 'none';
                var elements = document.getElementsByClassName("modal-backdrop");
                var i;
                for (i = 0; i < elements.length; i++) {
                    elements[i].parentNode.removeChild(elements[i]);
                }

                var body = document.getElementsByTagName("body");
                body.classList.remove("modal-open");
            }
        }
    }
</script>
