<form id="article-form" class="mt-4">
    <div class="row">
        <div class="col-md-8 mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" required placeholder="Enter article title">
        </div>

        <div class="col-md-4 mb-3">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <select class="form-select" id="category" name="category" required>
                <option value="">Select a category</option>
                <option value="Environment">Environment</option>
                <option value="Technology">Technology</option>
                <option value="Health">Health</option>
                <option value="Finance">Finance</option>
                <option value="History">History</option>
                <option value="Gaming">Gaming</option>
                <option value="Automotive">Automotive</option>
                <option value="Travel">Travel</option>
                <option value="Science">Science</option>
                <option value="Business">Business</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="author" name="author" required placeholder="Enter author name">
        </div>

        <div class="col-md-6 mb-3">
            <label for="published" class="form-label">Published Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="published" name="published" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
        <textarea class="form-control" id="content" name="content" rows="8" required
            placeholder="Enter article content"></textarea>
        <div class="form-text">Write the full article content here.</div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Create Article</button>
        <button type="reset" class="btn btn-outline-secondary">Reset Form</button>
    </div>
</form>