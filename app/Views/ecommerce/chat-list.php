          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

              <!-- Content -->

              <div class="container-xxl flex-grow-1 container-p-y">
                <div class="app-chat card overflow-hidden">
                  <div class="row g-0">
                    <!-- Chat & Contacts -->
                    <div
                      class="col app-chat-contacts app-sidebar flex-grow-0 overflow-hidden border-end"
                      id="app-chat-contacts">
                      <div class="sidebar-header">
                        <div class="d-flex align-items-center me-3 me-lg-0">
                          <div
                            class="flex-shrink-0 avatar avatar-online me-3"
                            data-bs-toggle="sidebar"
                            data-overlay="app-overlay-ex"
                            data-target="#app-chat-sidebar-left">
                            <img
                              class="user-avatar rounded-circle cursor-pointer"
                              src="<?= base_url('assets/img/avatars/1.png') ?>"
                              alt="Avatar" />
                          </div>
                          <div class="flex-grow-1 input-group input-group-merge rounded-pill">
                            <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                            <input
                              type="text"
                              class="form-control chat-search-input"
                              placeholder="Search..."
                              aria-label="Search..."
                              aria-describedby="basic-addon-search31" />
                          </div>
                        </div>
                        <i
                          class="ti ti-x cursor-pointer d-lg-none d-block position-absolute mt-2 me-1 top-0 end-0"
                          data-overlay
                          data-bs-toggle="sidebar"
                          data-target="#app-chat-contacts"></i>
                      </div>
                      <hr class="container-m-nx m-0" />
                      <div class="sidebar-body">
                        <div class="chat-contact-list-item-title">
                          <h5 class="text-primary mb-0 px-4 pt-3 pb-2">Chats</h5>
                        </div>
                        <!-- Chats -->
                        <ul class="list-unstyled chat-contact-list" id="chat-list">
                          <li class="chat-contact-list-item chat-list-item-0 d-none">
                            <h6 class="text-muted mb-0">No Chats Found</h6>
                          </li>
                        </ul>
                        <!-- Contacts -->
                        <ul class="list-unstyled chat-contact-list mb-0" id="contact-list">
                          <li class="chat-contact-list-item chat-contact-list-item-title">
                            <h5 class="text-primary mb-0">Contacts</h5>
                          </li>
                          <li class="chat-contact-list-item contact-list-item-0 d-none">
                            <h6 class="text-muted mb-0">No Contacts Found</h6>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <!-- /Chat contacts -->

                    <!-- Chat History -->
                    <div class="col app-chat-history bg-body">
                      <div class="chat-history-wrapper">
                        <div class="chat-history-header border-bottom">
                          <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex overflow-hidden align-items-center">
                              <i
                                class="ti ti-menu-2 ti-sm cursor-pointer d-lg-none d-block me-2"
                                data-bs-toggle="sidebar"
                                data-overlay
                                data-target="#app-chat-contacts"></i>
                              <div class="flex-shrink-0 avatar">
                                <img
                                  src="<?= base_url('assets/img/avatars/2.png') ?>"
                                  alt="Avatar"
                                  class="rounded-circle"
                                  data-bs-toggle="sidebar"
                                  data-overlay
                                  data-target="#app-chat-sidebar-right" />
                              </div>
                              <div class="chat-contact-info flex-grow-1 ms-2">
                                <h6 class="m-0">Felecia Rower</h6>
                                <small class="user-status text-muted">NextJS developer</small>
                              </div>
                            </div>
                            <div class="d-flex align-items-center">
                              <i class="ti ti-phone-call cursor-pointer d-sm-block d-none me-3"></i>
                            </div>
                          </div>
                        </div>
                        <div class="chat-history-body bg-body">
                          <ul class="list-unstyled chat-history" id="chat-history"></ul>
                        </div>
                        <!-- Chat message form -->
                        <div class="chat-history-footer shadow-sm">
                          <form class="form-send-message d-flex justify-content-between align-items-center">
                            <input
                              class="form-control message-input border-0 me-3 shadow-none"
                              placeholder="Type your message here" />
                            <div class="message-actions d-flex align-items-center">
                              <i class="speech-to-text ti ti-microphone ti-sm cursor-pointer"></i>
                              <label for="attach-doc" class="form-label mb-0">
                                <i class="ti ti-photo ti-sm cursor-pointer mx-3"></i>
                                <input type="file" id="attach-doc" hidden />
                              </label>
                              <button class="btn btn-primary d-flex send-msg-btn">
                                <i class="ti ti-send me-md-1 me-0"></i>
                                <span class="align-middle d-md-inline-block d-none">Send</span>
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <!-- /Chat History -->

                    <div class="app-overlay"></div>
                  </div>
                </div>
              </div>
              <!-- / Content -->

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>
            <script src="<?= base_url('assets/js/app-chat.js') ?>"></script>
          <?= $this->endSection() ?>