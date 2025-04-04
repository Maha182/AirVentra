<x-app-layout layout="landing" :isHeader1=true>
    <x-landing-pages.widgets.sub-header subTitle="Faq" subBreadcrume="Faq" />
    <div class="section-padding bg-white">
        <div class="container">
            <div class="row">
            <div class="col-lg-6 col-xl-6">
                <div class="mb-2 text-uppercase text-primary sub-title">
                   faq
                </div>
                <h2 class="text-secondary heading-title">Foremost Common  <span class="text-primary">Questions</span></h2>
                <p class="mb-0 mt-2">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat.</p>
            </div>
            <div class="col-lg-6 col-xl-6 mt-4 mt-lg-0">
                 <div class="accordion custom-accordion faq" id="accordionExample">
                    <div class="accordion-item mb-4 pb-2 border-bottom rounded-0">
                        <div class="accordion-header" id="headingOne">
                            <button class="accordion-button px-0 h5 pt-0 mb-0" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                How does SAASworld make money?
                            </button>
                        </div>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body px-0">
                                <p class="mb-0">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet
                                    sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-4 pb-2 border-bottom rounded-0">
                        <div class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed px-0 h5 mb-0" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                What projects can be done on SAASworld?
                            </button>
                        </div>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body px-0">
                                <p class="mb-0">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet
                                    sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-4 pb-2 border-bottom rounded-0">
                        <div class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed h5 px-0 mb-0" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                What is the Top Rated program?
                            </button>
                        </div>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body px-0">
                                <p class="mb-0">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet
                                    sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-4 pb-2 border-bottom rounded-0">
                        <div class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed h5 px-0 mb-0" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                What’s the difference between finding clients online locally?
                            </button>
                        </div>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body px-0">
                                <p class="mb-0">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet
                                    sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed h5 px-0 mb-0" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                Can I sell scripts, etc. written by others?
                            </button>
                        </div>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body px-0">
                                <p class="mb-0">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet
                                    sint. Velit officia consequat duis enim velit mollit. Exercitation veniam consequat.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="section-padding pt-0 bg-white">
        <x-landing-pages.widgets.contact-detail />
    </div>
</x-app-layout>
