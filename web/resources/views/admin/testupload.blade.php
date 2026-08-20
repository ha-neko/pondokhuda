<form action="{{route('admin.testupload')}}" method="POST" enctype="multipart/form-data">
	@csrf
	Upload foto : <input type="file" name="testupload">
	<input type="submit" name="upload" value="Upload">
</form>