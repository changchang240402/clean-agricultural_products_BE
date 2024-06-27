@extends('mails.layouts.main')

@section('content')
<table>
    <tr>
        <td align="center">
            <div class="container">
                <div class="content">
                    <div class="logo h-10 mr-2 flex font-bold justify-center w-full items-center font-roboto">
                        <h1 class="lobster">N</h1>
                        <div class="">
                            <img loading="lazy" src="https://techport.vn/uploads/2019/12-4/share_fb_home.png"
                                height="40px" width="40px" alt="" />
                        </div>
                        <h1 class="lobster">ng Sản Việt</h1>
                    </div>
                    <table class="content-table">
                        <tr>
                            <td>
                                <h1>{{ $data['title'] }}</h1>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="text">
                                    {{ $data['describe'] }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <a class="button"
                                        href='https://agriculturalvietnamese.io.vn/{{ $data['link'] }}'>Xem</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="text">
                                    <strong>Hãy quay lại trang.</strong>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
    </tr>
</table>
@endsection