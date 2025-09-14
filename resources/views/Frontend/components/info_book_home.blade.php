@push('styles')
    <style>
        /* card info */
        .info-card-home {
            background: #e7e7e7;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .text-justify {
            text-align: justify;
            text-justify: inter-word;
            word-break: normal;
            line-height: 1.8;
            margin-bottom: 1rem;
            hyphens: auto;
        }
    </style>
@endpush

<section id="info-book-home">
    <div class="container mt-3">
        <div class="info-card-home h-70">
            <h2 class="p-2 text-danger fw-bold">{{ ucfirst(request()->getHost()) }} là web chính chủ duy nhất của truyện.
            </h2>
            <h3><a href="https://www.youtube.com/channel/UClUgvbX62pg2XFPnRWaiSgQ" target="_blank" class="text-danger fw-bold text-decoration-none">Đón nghe truyện audio trên kênh Youtube: TruyenAkayHau</a></h2>
            <div class="p-4 row">
                <div class="col-12 col-md-3 d-flex justify-content-center mb-3 mb-md-0">
                    @include('components.book')
                </div>
                <div class="col-12 col-md-9">
                    <div class="my-3">
                        <h2 class="fw-bold">AKAY TRUYỆN</h2>
                        <span class="fw-bold">Tác giả: Akay Hau</span>
                        <p class="text-muted mt-4 mb-0 text-justify">Là thủ khoa với điểm thi đại học cao nhất toàn
                            quốc, lại trở thành một phế vật ở thế giới tu chân tàn khốc, nơi sức mạnh chính là luật lệ,
                            cá lớn nuốt cá bé, kẻ yếu chỉ như con kiến không có tiếng nói, bị người người giẫm đạp.</p>

                        <p class="text-muted m-0 text-justify">Hai mắt mù loà, thân phận hèn kém, vạn địch vây
                            quanh...thiếu niên trong tuyệt cảnh thức tỉnh Bá Chủ Hệ Thống, từ đó đặt chân vào lằn ranh
                            sinh tử trên con đường tu chân rực rỡ sắc màu.</p>

                        <p class="text-muted m-0 text-justify">Vạn tộc tung hoành, thiên kiêu như nấm, giai nhân làm
                            bạn, khoái ý ân cừu, hành tẩu giang hồ, nhất thống thiên địa...viết nên một khúc truyền kỳ
                            bất hủ. AKAY TRUYỆN là truyện Huyền Huyễn, Tiên Hiệp... được viết ra nhằm mục đích giải
                            trí.</p>

                        <p class="text-muted m-0 text-justify">Tất cả nội dung, tên nhân vật, địa danh, tôn giáo...trong
                            truyện đều là hư cấu, nếu có lỡ trùng hợp xin rộng lượng bỏ qua.</p>
                    </div>
                </div>


            </div>

        </div>
    </div>
</section>
