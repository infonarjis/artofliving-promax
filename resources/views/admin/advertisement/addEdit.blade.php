@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')
        <!-- Basic Layout -->
        <div class="row">
            <div class="col-xl-7">
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="{{ $formId }}" name="{{ $formName }}" action="{{ route($formUrl) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row g-6">
                                <?php echo $fromHtml; ?>
                            </div>
                            <button type="submit" class="btn btn-primary {{ $formSubmitBtnClass }}"
                                id="{{ $formSubmitBtnId }}">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card">
                    <div class="card-body">

                        <h5 class="mb-3">Advertisement Guide for Admin</h5>

                        <div class="alert alert-info">
                            Please read this guide before adding any advertisement. Follow the exact sizes and instructions
                            to avoid display issues on the website.
                        </div>

                        <!-- ================= Banner Advertisement Guide ================= -->
                        <h6 class="mt-4 text-primary">1) Banner Advertisement Guide</h6>

                        <p>
                            Choose <strong>"Banner Advertisement"</strong> when you want to upload your own promotional
                            image banner.
                        </p>

                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Level</th>
                                    <th>Banner Size (px)</th>
                                    <th>Where It Appears</th>
                                    <th>Best Use</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Level 1</strong></td>
                                    <td>300 × 300 px</td>
                                    <td>Left / Right sidebar of website</td>
                                    <td>Best for square promotional ads, offers, small campaigns</td>
                                </tr>
                                <tr>
                                    <td><strong>Level 2</strong></td>
                                    <td>300 × 600 px</td>
                                    <td>Left / Right sidebar (tall format)</td>
                                    <td>High visibility vertical ads, better for detailed promotions</td>
                                </tr>
                                <tr>
                                    <td><strong>Above Footer</strong></td>
                                    <td>1920 × 220 px</td>
                                    <td>Full width banner above website footer</td>
                                    <td>Best for brand promotion, large announcements, festive offers</td>
                                </tr>
                                <!-- Dashboard Banner -->
                                <tr>
                                    <td>
                                        <strong>Dashboard Banner</strong>
                                    </td>
                                    <td>
                                        1056 × 287 px
                                    </td>
                                    <td>
                                        Dashboard main content area
                                    </td>
                                    <td>
                                        Matrimonial promotions,
                                        featured campaigns and
                                        important announcements
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <ul class="mt-2">
                            <li>Allowed image types: JPG, PNG, GIF, BMP</li>
                            <li>Maximum file upload size: 10MB</li>
                            <li>Keep important content in the center of the banner</li>
                            <li>You must provide a valid redirect <strong>Link</strong></li>
                        </ul>

                        <!-- ================= Google AdSense Guide ================= -->
                        <h6 class="mt-3 text-success">2) Google AdSense Advertisement Guide</h6>
                        <p>
                            Choose <strong>"Google AdSense Advertisement"</strong> when you want to display ads provided by
                            your
                            <strong>Google AdSense account</strong> instead of uploading an image.
                        </p>

                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Field</th>
                                    <th>What Admin Should Do</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>AdSense Code</strong></td>
                                    <td>Copy the Ad Unit code from Google AdSense and paste it into the textarea</td>
                                </tr>
                                <tr>
                                    <td><strong>Banner Image</strong></td>
                                    <td>Not required for AdSense ads</td>
                                </tr>
                                <tr>
                                    <td><strong>Link</strong></td>
                                    <td>Not required (Google automatically handles ad redirection)</td>
                                </tr>
                                <tr>
                                    <td><strong>Ad Position</strong></td>
                                    <td>The ad will display in the selected Level position</td>
                                </tr>
                            </tbody>
                        </table>

                        <ul class="mt-2">
                            <li>Do not edit or modify the AdSense script</li>
                            <li>Ads may take some time to appear after adding the code</li>
                            <li>Ad display depends on Google policies and user eligibility</li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
