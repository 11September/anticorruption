<div class="modal fade" id="Modal1" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-wrapper">
                <h1>Експорт обраних даних:</h1>


                <div class="export-all-text">Обрати всі
                    <div class="check-all-button"><input type="checkbox" class="" name="" value=""
                                                         onclick="selectAll(this)"></div>
                </div>

                <div class="clearfix"></div>

                <form class="export-download-objects" action="/export-download" method="POST">
                    {{ csrf_field() }}

                    <div class="wrapper-form" id="checkboxesWrapper">

                        <div class="clearfix"></div>

                        @foreach($objects as $object)
                            <div class="export-download-object-item">
                                <div class="export-download-object-item-text"><span> {{ $object->name }} за адресою <i> {{ $object->address }} </i></span></div>
                                <div class="export-download-object-item-input">
                                    <input type="checkbox" class="objectCheckbox" name="save[]" value="{{ $object->id }}">
                                </div>
                            </div>
                        @endforeach

                        <div class="clearfix"></div>

                        @if(count($errors))
                            <script defer>
                                $(function redirectShow() {
                                    $('#Modal1').modal('show');
                                });
                            </script>
                        @endif



                    </div>
                    <div class="exp-variants">
                        <div class="wrapper-button-to-save-files">
                            <div class="variant-cont">
                                <button class="exp-var btn btn-primary" type="submit" name="download" value="pdf"
                                        id="pdf_download">
                                    PDF
                                </button>
                            </div>
                            <div class="variant-cont">
                                <button class="exp-var btn btn-primary" type="submit" name="download" value="csv"
                                        id="csv_download">
                                    CSV
                                </button>
                            </div>
                            <div class="variant-cont">
                                <button class="exp-var btn btn-primary" type="submit" name="download" value="xls"
                                        id="exel_download">
                                    EXCEL
                                </button>
                            </div>
                            <div class="variant-cont">
                                <button class="exp-var btn btn-primary" type="submit" name="download" value="word"
                                        id="word_download">
                                    WORD
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="errorDiv">
                        @foreach($errors->all() as $error )
                            <p class="export-error">{{ $error }}</p>
                        @endforeach
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>