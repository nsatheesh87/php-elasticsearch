<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Instagram APP</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ url('/css/app.css') }}" rel="stylesheet">
    <style type="text/css">
        body {
            padding-top: 54px;
        }

        @media (min-width: 992px) {
            body {
                padding-top: 56px;
            }
        }
        .img-responsive {
            min-width: 100%;
        }
        .post {
            margin-bottom: 60px;
            border-radius: 3px;
            border: 1px solid #e6e6e6;
            background-color: #fff;
            padding: 5px;
        }

        .profile-header img{
           height: 30px;
            width: 30px;
        }
        .wrapper {
            margin-top:20px;
        }
        .page-load-status {
            display: none; /* hidden by default */
        }
    </style>

</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Instagram APP</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container">

    <div class="row">

        <div class="col-lg-3">

            <h2 class="my-4">Left Bar</h2>
            <div class="list-group">
                <a href="{{ url('/hot') }}" class="list-group-item">Hot</a>
                <a href="{{ url('/recent') }}" class="list-group-item">Recent</a>
            </div>

        </div>
        <!-- /.col-lg-3 -->

        <div class="col-lg-6">
                <div class="wrapper">
                    @if(!empty($feeds))
                        @foreach ($feeds['hits'] as $feed)
                            <div class="post">
                                <h5 class="profile-header"> <img src="{{$feed['_source']['profile_picture']}}" /> {{$feed['_source']['username']}}</h5>
                                <p><img class="img-responsive" src="{{$feed['_source']['post']}}" alt="orange-tree" /></p>
                                @if (!empty($feed['_source']['caption']))
                                   <p>
                                      <?php $caption = preg_replace('/(?:^|\s)#(\w+)/', ' <a href="tag/$1">#$1</a>', $feed['_source']['caption']['text']);
                                      print $caption;
                                      ?>
                                   </p>
                                @endif
                                <p>
                                    {{$feed['_source']['likes_count']}} likes &nbsp; {{$feed['_source']['comments_count']}} Comments
                                </p>
                                <p>{{\Carbon\Carbon::createFromTimestamp($feed['_source']['created_time'])->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="page-load-status">
                   Please wait while we are loading your feeds ..
                    <p class="infinite-scroll-last">No More feeds available to display</p>
                    <p class="infinite-scroll-error">No more pages to load</p>
                </div>
            <!-- /.row -->

        </div>
        <!-- /.col-lg-9 -->
        <div class="col-lg-3">

            <h2 class="my-4">Right Side Bar</h2>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->
</div>
    <!-- Footer -->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Your Website 2018</p>
        </div>
        <!-- /.container -->
    </footer>
    <script src="{{ url('/js/app.js') }}"></script>
    <script src="https://unpkg.com/infinite-scroll@3/dist/infinite-scroll.pkgd.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var totalPages = {{ceil($feeds['total']/4)}}
            var fType = '{{$sort}}';

            function getPath() {
                var loopCount = this.loadCount+1;

                if(loopCount < totalPages) {
                    return 'http://localhost:8080/instagram/fetch/'+loopCount+'/'+fType;
                }

            }

            $('.wrapper').infiniteScroll({
                // options
                path: getPath,
                append: '.post',
                status: '.page-load-status',
                history: false,
            });

        });
    </script>

</body>

</html>
