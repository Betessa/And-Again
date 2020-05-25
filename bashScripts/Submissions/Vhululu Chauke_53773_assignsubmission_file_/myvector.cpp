#include "myvector.h"
bool Thing::verbose = false;
size_t Thing::last_alloc = 0;


MyVector::MyVector()
{

 	n_items = 0;
	data = nullptr;
	n_allocated = 0;
}


MyVector::~MyVector()
{
    
}

size_t MyVector::size() const
{
	return n_items;
}


size_t MyVector::allocated_length() const
{
	return n_allocated;
}


void MyVector::push_back(const Thing &t)
{
	
	if(n_allocated == 0){
		reallocate(1);
		}
	else if(n_allocated == n_items){
		reallocate(2*n_allocated);
		}
		data[n_items]= t;
		++n_items ;
}


void MyVector::pop_back()
{
		--n_items;
	if(n_items < n_allocated/4){
		reallocate(n_allocated/2);
		}
		
}


Thing &MyVector::front()
{
	return data[0];
}


Thing &MyVector::back()
{
	return data[n_items-1];
}


Thing *MyVector::begin()
{
	return data;
}


Thing *MyVector::end()
{
	return data + n_items;
}


Thing &MyVector::operator[](size_t i)
{
	return data[i];
}


Thing &MyVector::at(size_t i)
{
if(i >= n_items){
throw std::out_of_range("error");
	}
else{	return data[i];
}
}


void MyVector::reallocate(size_t new_size)
{
	Thing*tmp = new Thing[new_size];
	for(size_t i = 0; i<n_items; ++i){
	tmp[i]= data[i];
	}
	delete[] data;
	data = tmp;
n_allocated = new_size;

}

